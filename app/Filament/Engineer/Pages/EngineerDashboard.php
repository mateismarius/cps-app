<?php

namespace App\Filament\Engineer\Pages;

use Filament\Pages\Page;

use Filament\Pages\Dashboard as BaseDashboard;



class EngineerDashboard extends BaseDashboard
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.engineer.pages.engineer-dashboard';

    protected static ?string $title = 'Engineer Portal';

    protected static ?string $navigationLabel = 'Dashboard';


}
