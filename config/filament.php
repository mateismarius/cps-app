<?php
return [
    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    'navigation' => [
        'groups' => [
            'business' => [
                'label' => 'Business',
                'icon' => 'heroicon-o-building-office-2',
                'collapsed' => false,
            ],
            'operations' => [
                'label' => 'Operations',
                'icon' => 'heroicon-o-briefcase',
                'collapsed' => false,
            ],
            'hr' => [
                'label' => 'Human Resources',
                'icon' => 'heroicon-o-users',
                'collapsed' => false,
            ],
            'finance' => [
                'label' => 'Finance',
                'icon' => 'heroicon-o-banknotes',
                'collapsed' => false,
            ],
            'assets' => [
                'label' => 'Assets',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'collapsed' => true,
            ],
        ],
    ],
];
