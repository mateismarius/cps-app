<?php

namespace App\Filament\Resources\Reports\Schemas;

use App\Models\Timesheet;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Report Information')
                    ->schema([
                        Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('timesheet_id', null);
                                $set('engineer_id', null);
                            })
                            ->columnSpan(2),

                        Select::make('timesheet_id')
                            ->label('Timesheet')
                            ->options(function (callable $get) {
                                $projectId = $get('project_id');

                                if (!$projectId) {
                                    return [];
                                }

                                return Timesheet::query()
                                    ->where('project_id', $projectId)
                                    ->with(['engineer', 'schedule'])
                                    ->get()
                                    ->mapWithKeys(function ($timesheet) {
                                        $label = "#{$timesheet->id} - {$timesheet->engineer->name} - " .
                                            $timesheet->date->format('d/m/Y');
                                        return [$timesheet->id => $label];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $timesheet = Timesheet::find($state);
                                    if ($timesheet) {
                                        $set('engineer_id', $timesheet->engineer_id);
                                        $set('report_date', $timesheet->date);
                                    }
                                }
                            })
                            ->helperText('Select a timesheet to automatically fill engineer and date')
                            ->columnSpan(2),

                        Select::make('engineer_id')
                            ->label('Engineer')
                            ->relationship('engineer', 'name')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->columnSpan(2),

                        DatePicker::make('report_date')
                            ->label('Report Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Section::make('Report Content')
                    ->schema([
                        Textarea::make('summary')
                            ->label('Summary')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Provide a detailed summary of the work performed'),

                        FileUpload::make('file_path')
                            ->label('Attachment')
                            ->directory('reports')
                            ->disk('public')
                            ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(10240) // 10MB
                            ->downloadable()
                            ->openable()
                            ->previewable()
                            ->columnSpanFull()
                            ->helperText('Upload supporting documents (PDF, Images, Word documents). Max 10MB'),
                    ]),

                Section::make('Timesheet Details')
                    ->schema([
                        TextEntry::make('timesheet_details')
                            ->label('Related Timesheet Information')
                            ->state(function (callable $get) {
                                $timesheetId = $get('timesheet_id');

                                if (!$timesheetId) {
                                    return 'No timesheet selected';
                                }

                                $timesheet = Timesheet::with(['schedule', 'engineer'])->find($timesheetId);

                                if (!$timesheet) {
                                    return 'Timesheet not found';
                                }

                                $details = "Engineer: {$timesheet->engineer->name}\n";
                                $details .= "Date: {$timesheet->date->format('d/m/Y')}\n";
                                $details .= "Status: " . ($timesheet->approved ? 'Approved' : 'Pending') . "\n";

                                if ($timesheet->schedule) {
                                    $details .= "Shift: {$timesheet->schedule->shift_start} - {$timesheet->schedule->shift_end}\n";
                                    if ($timesheet->schedule->location) {
                                        $details .= "Location: {$timesheet->schedule->location}";
                                    }
                                }

                                return nl2br($details);
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (callable $get) => $get('timesheet_id'))
                    ->collapsible(),
            ]);
    }
}
