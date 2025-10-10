<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\CreateProjects;
use App\Filament\Resources\Projects\Pages\EditProjects;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Resources\Projects\Pages\ViewProjects;
use App\Filament\Resources\Projects\Schemas\ProjectsForm;
use App\Filament\Resources\Projects\Schemas\ProjectsInfolist;
use App\Filament\Resources\Projects\Tables\ProjectsTable;
use App\Models\Project;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectsResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|null|BackedEnum $navigationIcon = Heroicon::Briefcase;

    protected static string|null|\UnitEnum $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ProjectsForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProjects::route('/create'),
            'view' => ViewProjects::route('/{record}'),
            'edit' => EditProjects::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
