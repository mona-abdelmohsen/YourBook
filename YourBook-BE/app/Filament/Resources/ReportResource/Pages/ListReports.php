<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkAction;


// class ListReports extends ListRecords
// {
//     protected static string $resource = ReportResource::class;

//     protected function getHeaderActions(): array
//     {
//         return [
//             Actions\CreateAction::make(),
//         ];
//     }
// }

// namespace App\Filament\Resources\ReportResource\Pages;

// use Filament\Resources\Pages\ListRecords;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    protected function getActions(): array
    {
        // Return an empty array to remove all actions
        return [];
    }


    // protected function getBulkActions(): array
    // {
    //     return [
    //         BulkAction::make('delete')
    //             ->label('Delete')
    //             ->action(fn (array $records) => $this->deleteRecords($records))
    //             ->requiresConfirmation()
    //             ->color('danger')
    //             ->icon('heroicon-o-trash'),
    //     ];
    // }

    // private function deleteRecords(array $records): void
    // {
    //     foreach ($records as $recordId) {
    //         $record = static::$resource::getModel()::findOrFail($recordId);
    //         $record->delete();
    //     }
    // }
}
