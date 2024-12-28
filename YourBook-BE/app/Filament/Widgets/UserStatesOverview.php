<?php

namespace App\Filament\Widgets;

use App\Models\Auth\User;
use App\Models\Posts\Post;
use App\Models\Book;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::count())
                ->description('Total Users')
                ->descriptionIcon('heroicon-m-users'),
            Stat::make('Posts', Post::count())
                ->description('Total Posts')
                ->descriptionIcon('heroicon-m-document-text'),
            Stat::make('Books', Book::count())
                ->description('Total Books')
                ->descriptionIcon('heroicon-m-book-open'),
        ];
    }
}
