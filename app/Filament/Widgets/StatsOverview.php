<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use Illuminate\Support\Facades\Cache;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        return Cache::remember('filament_stats_overview', 60, function () {
            return [
                Stat::make('Total Users', \App\Models\User::count())
                    ->description('Registered students')
                    ->descriptionIcon('heroicon-m-arrow-trending-up')
                    ->chart([7, 2, 10, 3, 15, 4, 17])
                    ->color('success')
                    ->url(route('filament.admin.resources.users.index')),

                Stat::make('Active Enrollments', \App\Models\Enrollment::where('status', 'active')->count())
                    ->description('Students currently learning')
                    ->descriptionIcon('heroicon-m-academic-cap')
                    ->chart([15, 4, 10, 2, 12, 4, 12])
                    ->color('primary')
                    ->url(route('filament.admin.resources.enrollments.index')),

                Stat::make('Total Courses', \App\Models\Course::count())
                    ->description('Available programs')
                    ->descriptionIcon('heroicon-m-book-open')
                    ->color('warning')
                    ->url(route('filament.admin.resources.courses.index')),
                
                Stat::make('Completed Quizzes', \App\Models\QuizAttempt::where('status', 'completed')->count())
                    ->description('Tests passed')
                    ->descriptionIcon('heroicon-m-check-badge')
                    ->chart([3, 5, 2, 8, 10, 15, 20])
                    ->color('info')
                    ->url(route('filament.admin.resources.quiz-attempts.index')),
            ];
        });
    }
}
