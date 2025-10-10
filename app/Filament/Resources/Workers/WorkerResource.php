<?php

namespace App\Filament\Resources\Workers;

use App\Filament\Resources\Workers\Pages\CreateWorker;
use App\Filament\Resources\Workers\Pages\EditWorker;
use App\Filament\Resources\Workers\Pages\ListWorkers;
use App\Filament\Resources\Workers\Pages\ViewWorker;
use App\Filament\Resources\Workers\Schemas\WorkerForm;
use App\Filament\Resources\Workers\Schemas\WorkerInfolist;
use App\Filament\Resources\Workers\Tables\WorkersTable;
use App\Models\Worker;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;
    protected static string|null|\UnitEnum $navigationGroup = 'HR';

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Engineers';

    protected static ?string $recordTitleAttribute = 'Engineer';

    // Etichete globale folosite în titluri, butoane, acțiuni, empty states etc.
    public static function getModelLabel(): string
    {
        return 'engineer';        // singular (ex: "Create engineer")
    }

    public static function getPluralModelLabel(): string
    {
        return 'engineers';       // plural (ex: "All engineers")
    }

    public static function form(Schema $schema): Schema
    {
        return WorkerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WorkerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkersTable::configure($table);
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
            'index' => ListWorkers::route('/'),
            'create' => CreateWorker::route('/create'),
            'view' => ViewWorker::route('/{record}'),
            'edit' => EditWorker::route('/{record}/edit'),
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
