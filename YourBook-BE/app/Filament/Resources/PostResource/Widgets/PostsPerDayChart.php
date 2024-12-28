<?php

namespace App\Filament\Resources\PostResource\Widgets;
use App\Models\Posts\Post;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostsPerDayChart extends LineChartWidget
{
    protected static ?string $heading = 'Posts Per Day';

    protected function getData(): array
    {
        $data = Post::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Posts',
                    'data' => $data->pluck('count'),
                ],
            ],
            'labels' => $data->pluck('date')->map(fn ($date) => Carbon::parse($date)->format('M d')),
        ];
    }
}
