<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Auth\User;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewUsersPerDayChart extends LineChartWidget
{
    protected static ?string $heading = 'New Users Per Day';

    protected function getData(): array
    {
        $data = User::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $data->pluck('count'),
                ],
            ],
            'labels' => $data->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d')),
        ];
    }
}
