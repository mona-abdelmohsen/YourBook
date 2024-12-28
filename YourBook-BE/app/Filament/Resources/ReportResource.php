<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Reports\Report;
use App\Models\Auth\User;
use App\Models\Posts\Post;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\Button;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Http\Client\Request;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Group::make([
                Forms\Components\TextInput::make('reason')
                    ->label('Reason')
                    ->maxLength(255),

                Forms\Components\Textarea::make('meta')
                    ->label('Meta Data')
                    // ->json()
                    ->rows(6),

                Forms\Components\TextInput::make('reportable_type')
                    ->label('Reportable Type')
                    ->disabled(),

                Forms\Components\TextInput::make('reportable_id')
                    ->label('Reportable ID')
                    ->disabled(),
            ])
            ->columns(2),
            
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->query(Report::with('reporter'))  
            ->columns([
                TextColumn::make('reason')
                    ->label('Reason')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('reporter.name')
                    ->label('Reporter Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('reportable_type')
                    ->label('Reported Item Type')
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->disabled(),

                TextColumn::make('reportable_id')
                    ->label('Reported Item ID')->disabled(),

                TextColumn::make('meta')
                    ->label('Meta Data')
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return 'N/A';
                        }

                        $decoded = json_decode($state, true);

                        if (is_array($decoded)) {
                            return implode(', ', $decoded);
                        }

                        return $state;
                    })
                    ->html()
                    ->searchable(),

                TextColumn::make('conclusion.conclusion')
                    ->label('Conclusion')
                    ->searchable(),

                TextColumn::make('conclusion.action_taken')
                    ->label('Action Taken')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('reportable_type')
                    ->label('Reported Item Type')
                    ->options([
                        Post::class => 'Post',
                        Book::class => 'Book',
                        User::class => 'User',
                    ]),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->label('Delete with Conclusion')
                    ->action(function (Collection $records, array $data) {
                        foreach ($records as $record) {
                            // Store the conclusion and action in the reports_conclusions table
                            $record->conclude([
                                'conclusion' => $data['conclusion'],
                                'action_taken' => $data['action'],
                            ], auth()->user());

                            if ($data['action'] === 'delete') {
                                $record->delete();
                            }
                        }

                        // Notify the user
                        Notification::make()
                            ->title('Action performed successfully.')
                            ->success()
                            ->send();
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
                    ->icon('heroicon-o-trash')


            ])->actions([
                    Tables\Actions\EditAction::make(), 
                ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'edit' => Pages\EditReport::route('/{record}/edit')
        ];
    }
}
