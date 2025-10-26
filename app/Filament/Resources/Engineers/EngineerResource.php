<?php

namespace App\Filament\Resources\Engineers;

use App\Filament\Resources\Engineers\Pages\CreateEngineer;
use App\Filament\Resources\Engineers\Pages\EditEngineer;
use App\Filament\Resources\Engineers\Pages\ListEngineers;
use App\Filament\Resources\Engineers\Pages\ViewEngineer;
use App\Filament\Resources\Engineers\Schemas\EngineerForm;
use App\Filament\Resources\Engineers\Schemas\EngineerInfolist;
use App\Filament\Resources\Engineers\Tables\EngineersTable;
use App\Models\Engineer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EngineerResource extends Resource
{
    protected static ?string $model = Engineer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Engineer';

    protected static string | UnitEnum | null $navigationGroup = 'Business';

    public static function form(Schema $schema): Schema
    {
        return EngineerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EngineerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EngineersTable::configure($table);
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
            'index' => ListEngineers::route('/'),
            'create' => CreateEngineer::route('/create'),
            'view' => ViewEngineer::route('/{record}'),
            'edit' => EditEngineer::route('/{record}/edit'),
        ];
    }
}
