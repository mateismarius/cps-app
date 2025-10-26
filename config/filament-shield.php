<?php
// config/filament-shield.php
return [
    // ...
    'auth_provider_model' => 'App\\Models\\User',
    'navigationGroup' => 'Administration',
    // ...

    'super_admin' => [
        'enabled' => true,
        'name' => 'super_admin',
    ],

];
