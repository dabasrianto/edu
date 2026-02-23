<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

use Illuminate\Support\Facades\Cache;

class EnrollmentChart extends ChartWidget
{
    protected ?string $heading = 'Student Enrollments';
    
    protected ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        return Cache::remember('filament_enrollment_chart', 600, function () {
            $data = \App\Models\Enrollment::where('created_at', '>=', now()->subMonths(6))
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
    
            return [
                'datasets' => [
                    [
                        'label' => 'New Students',
                        'data' => $data->pluck('count')->toArray(),
                        'fill' => 'start',
                        'tension' => 0.4,
                        'backgroundColor' => 'rgba(59, 130, 246, 0.2)', // Blue
                        'borderColor' => 'rgb(59, 130, 246)',
                    ],
                ],
                'labels' => $data->pluck('month')->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y'))->toArray(),
            ];
        });
    }

    protected function getType(): string
    {
        return 'line';
    }
}
