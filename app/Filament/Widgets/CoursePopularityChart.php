<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

use Illuminate\Support\Facades\Cache;

class CoursePopularityChart extends ChartWidget
{
    protected ?string $heading = 'Popular Courses';
    
    protected ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        return Cache::remember('filament_course_popularity', 600, function () {
            $courses = \App\Models\Course::withCount('enrollments')
                ->orderByDesc('enrollments_count')
                ->take(5)
                ->get();
    
            return [
                'datasets' => [
                    [
                        'label' => 'Total Students',
                        'data' => $courses->pluck('enrollments_count')->toArray(),
                        'backgroundColor' => [
                            'rgba(255, 99, 132, 0.5)',
                            'rgba(54, 162, 235, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(153, 102, 255, 0.5)',
                        ],
                    ],
                ],
                'labels' => $courses->pluck('title')->toArray(),
            ];
        });
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
