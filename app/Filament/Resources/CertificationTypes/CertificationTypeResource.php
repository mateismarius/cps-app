<?php

namespace App\Filament\Resources\CertificationTypes;

use App\Filament\Resources\CertificationTypes\Pages\CreateCertificationType;
use App\Filament\Resources\CertificationTypes\Pages\EditCertificationType;
use App\Filament\Resources\CertificationTypes\Pages\ListCertificationTypes;
use App\Filament\Resources\CertificationTypes\Schemas\CertificationTypeForm;
use App\Filament\Resources\CertificationTypes\Tables\CertificationTypesTable;
use App\Models\CertificationType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CertificationTypeResource extends Resource
{
    protected static ?string $model = CertificationType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Certification Type';

    public static function form(Schema $schema): Schema
    {
        return CertificationTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertificationTypesTable::configure($table);
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
            'index' => ListCertificationTypes::route('/'),
            'create' => CreateCertificationType::route('/create'),
            'edit' => EditCertificationType::route('/{record}/edit'),
        ];
    }
}
