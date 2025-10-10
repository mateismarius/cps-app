<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

use App\Models\Project;

class ProjectsChart extends ChartWidget
{
    protected ?string $heading = 'Projects Overview';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $statuses = Project::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Projects by Status',
                    'data' => array_values($statuses),
                    'backgroundColor' => [
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(255, 159, 64)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                    ],
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($statuses)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
