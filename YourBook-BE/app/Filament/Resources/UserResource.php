<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Widgets\NewUsersPerDayChart;
use App\Models\Auth\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getWidgets(): array
    {
        return [
            NewUsersPerDayChart::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\MarkdownEditor::make('about'),
                Forms\Components\TextInput::make('phone'),
                Forms\Components\Select::make('gender')
                    ->options([
                        '0' => 'Male',
                        '1' => 'Female',
                    ]),
                Forms\Components\Select::make('privacy')
                    ->options([
                        'public' => 'Public',
                        'private' => 'Private',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\SelectColumn::make('privacy')
                    ->options([
                        'public' => 'Public',
                        'private' => 'Private',
                    ])
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('enable')
                    ->label('Enabled')
                    ->sortable()
                    ->updateStateUsing(fn($record, $state) => $record->update(['enable' => $state]))            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Active')
                    ->query(fn($query) => $query->where('enable', true)),
                Tables\Filters\Filter::make('disabled')
                    ->label('Disabled')
                    ->query(fn($query) => $query->where('enable', false)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('disable')
                    ->label('Disable')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn($record) => $record->enable)
                    ->action(fn($record) => $record->update(['enable' => false]))
                    ->requiresConfirmation()
                    ->color('danger'),

                Tables\Actions\Action::make('enable')
                    ->label('Enable')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn($record) => !$record->enable)
                    ->action(fn($record) => $record->update(['enable' => true]))
                    ->requiresConfirmation()
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('Enable Selected')
                        ->action(fn($records) => $records->each->update(['enable' => true]))
                        ->color('success'),

                    Tables\Actions\BulkAction::make('Disable Selected')
                        ->action(fn($records) => $records->each->update(['enable' => false]))
                        ->color('danger'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}