<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
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
            ->query(User::withTrashed())  
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\SelectColumn::make('privacy')
                    ->options([
                        'public' => 'Public',
                        'private' => 'Private',
                    ])->searchable()->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')->label('Disabled')->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Active')
                    ->query(fn($query) => $query->whereNull('deleted_at')),
                Tables\Filters\Filter::make('disabled')
                    ->label('Disabled')
                    ->query(fn($query) => $query->whereNotNull('deleted_at')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('disable')
                    ->label('Disable')
                    ->icon('heroicon-o-x-circle') // Try using a different icon name
                    ->visible(fn($record) => $record->deleted_at === null)
                    ->action(function ($record) {
                        $record->delete(); // Soft delete
                    })
                    ->requiresConfirmation()
                    ->color('danger'),

                Tables\Actions\Action::make('enable')
                    ->label('Enable')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn($record) => $record->deleted_at !== null)  // Visible only for disabled (soft-deleted) users
                    ->action(function ($record) {
                        $record->restore(); // Restore the soft-deleted user
                    })
                    ->requiresConfirmation()
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            //            'create' => Pages\CreateUser::route('/create'),
//            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
