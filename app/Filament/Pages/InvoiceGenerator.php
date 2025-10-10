<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\Subcontractor;
use App\Models\Timesheet;
use App\Services\InvoiceService;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class InvoiceGenerator extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-document-plus';

    protected string $view = 'filament.pages.invoice-generator';

    protected static string|null|\UnitEnum $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Generate Invoice';

    public ?array $data = [];

    public $clientId = null;
    public $subcontractorId = null;
    public $invoiceType = 'client';
    public $selectedTimesheets = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invoice Type')
                    ->schema([
                        Radio::make('invoiceType')
                            ->label('Type')
                            ->options([
                                'client' => 'Client Invoice',
                                'subcontractor' => 'Subcontractor Invoice',
                            ])
                            ->inline()
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->clientId = null;
                                $this->subcontractorId = null;
                                $this->selectedTimesheets = [];
                            }),
                    ]),

                Section::make('Select Entity')
                    ->schema([
                        Select::make('clientId')
                            ->label('Client')
                            ->options(Client::where('status', 'active')->pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->visible(fn ($get) => $get('invoiceType') === 'client')
                            ->afterStateUpdated(fn () => $this->selectedTimesheets = []),
                        Select::make('subcontractorId')
                            ->label('Subcontractor')
                            ->options(Subcontractor::where('status', 'active')->pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->visible(fn ($get) => $get('invoiceType') === 'subcontractor')
                            ->afterStateUpdated(fn () => $this->selectedTimesheets = []),
                    ]),
            ])
            ->statePath('data');
    }

    public function getUninvoicedTimesheets()
    {
        if ($this->invoiceType === 'client' && $this->clientId) {
            return Timesheet::whereHas('project', function ($query) {
                $query->where('client_id', $this->clientId);
            })
                ->where('status', 'approved')
                ->whereDoesntHave('invoiceItems')
                ->with(['worker', 'project'])
                ->get();
        }

        if ($this->invoiceType === 'subcontractor' && $this->subcontractorId) {
            return Timesheet::whereHas('worker', function ($query) {
                $query->where('subcontractor_id', $this->subcontractorId);
            })
                ->where('status', 'approved')
                ->whereDoesntHave('invoiceItems')
                ->with(['worker', 'project'])
                ->get();
        }

        return collect();
    }

    public function generateInvoice()
    {
        if (empty($this->selectedTimesheets)) {
            Notification::make()
                ->title('No timesheets selected')
                ->danger()
                ->send();
            return;
        }

        $invoiceService = app(InvoiceService::class);

        try {
            if ($this->invoiceType === 'client' && $this->clientId) {
                $client = Client::findOrFail($this->clientId);
                $invoice = $invoiceService->generateClientInvoice(
                    $client,
                    $this->selectedTimesheets
                );
            } elseif ($this->invoiceType === 'subcontractor' && $this->subcontractorId) {
                $subcontractor = Subcontractor::findOrFail($this->subcontractorId);
                $invoice = $invoiceService->generateSubcontractorInvoice(
                    $subcontractor,
                    $this->selectedTimesheets
                );
            }

            Notification::make()
                ->title('Invoice generated successfully')
                ->success()
                ->body("Invoice {$invoice->invoice_number} has been created.")
                ->send();

            // Reset form
            $this->selectedTimesheets = [];

            // Redirect to invoice view
            return redirect()->route('filament.admin.resources.invoices.view', ['record' => $invoice->id]);

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error generating invoice')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }
}
