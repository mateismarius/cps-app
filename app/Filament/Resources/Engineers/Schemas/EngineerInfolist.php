<?php

namespace App\Filament\Resources\Engineers\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EngineerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('company.name')
                    ->label('Company'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('trade.name')
                    ->label('Trade')
                    ->placeholder('-'),
                IconEntry::make('direct_to_main')
                    ->boolean(),
                TextEntry::make('rate_to_subcontractor')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('rate_to_main')
                    ->numeric(),
                IconEntry::make('active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
