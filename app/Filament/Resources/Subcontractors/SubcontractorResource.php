<?php

namespace App\Filament\Resources\Subcontractors;

use App\Filament\Resources\Subcontractors\Pages\CreateSubcontractor;
use App\Filament\Resources\Subcontractors\Pages\EditSubcontractor;
use App\Filament\Resources\Subcontractors\Pages\ListSubcontractors;
use App\Filament\Resources\Subcontractors\Pages\ViewSubcontractor;
use App\Filament\Resources\Subcontractors\Schemas\SubcontractorForm;
use App\Filament\Resources\Subcontractors\Schemas\SubcontractorInfolist;
use App\Filament\Resources\Subcontractors\Tables\SubcontractorsTable;
use App\Models\Subcontractor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubcontractorResource extends Resource
{
    protected static ?string $model = Subcontractor::class;

    protected static string|null|BackedEnum $navigationIcon = 'heroicon-o-user-group';

    protected static string|null|\UnitEnum $navigationGroup = 'Business';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'Subcontractor';

    public static function form(Schema $schema): Schema
    {
        return SubcontractorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubcontractorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubcontractorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WorkersRelationManager::class,
//            RelationManagers\CertificationsRelationManager::class,
//            RelationManagers\InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubcontractors::route('/'),
            'create' => CreateSubcontractor::route('/create'),
            'view' => ViewSubcontractor::route('/{record}'),
            'edit' => EditSubcontractor::route('/{record}/edit'),
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
