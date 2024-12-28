<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Radio;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('deleteWithConclusion')
                ->label('Delete with Conclusion')
                ->action(function (array $data) {
                    $record = $this->record;

                    // Store the conclusion and action in the reports_conclusions table
                    $record->conclude([
                        'conclusion' => $data['conclusion'],
                        'action_taken' => $data['action'],
                    ], auth()->user());

                    // Perform the delete action if specified
                    if ($data['action'] === 'delete') {
                        $record->delete();
                    }

                    // Notify the user
                    Notification::make()
                        ->title('Action performed successfully.')
                        ->success()
                        ->send();

                    $this->redirect(ReportResource::getUrl('index'));
                })
                ->requiresConfirmation()
                ->form([
                    Textarea::make('conclusion')
                        ->label('Conclusion')
                        ->required()
                        ->placeholder('Write your conclusion here...'),
                    Radio::make('action')
                        ->label('Action')
                        ->options([
                            'delete' => 'Delete',
                            'ignore' => 'Ignore',
                        ])
                        ->required(),
                ])
                ->modalHeading('Provide Conclusion and Action')
                ->color('danger')
                ->icon('heroicon-o-trash'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure that you are returning the correct data before saving
        return $data;
    }

    // Use the saved method to hook after the save
    protected function saved(): void
    {
        // Send the success notification
        Notification::make()
            ->title('Success')
            ->body('Report updated successfully!')
            ->success()
            ->send();
    }
}
