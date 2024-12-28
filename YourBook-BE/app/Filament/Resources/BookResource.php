<?php

namespace App\Filament\Resources;

use App\Enum\PrivacyEnum;
use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\Widgets\BooksPerDayChart;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    public static function getWidgets(): array
{
    return [
        BooksPerDayChart::class,
    ];
}

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->label('Book Title'),
                Textarea::make('description')
                    ->label('Description'),
                    Select::make('privacy')
                    ->label('Privacy')
                    ->options([
                        PrivacyEnum::PUBLIC->value => 'Public',
                        PrivacyEnum::PRIVATE->value => 'Private',
                        PrivacyEnum::FRIENDS->value => 'Friends',
                        PrivacyEnum::FRIENDS_OF_FRIENDS->value => 'Friends of Friends',
                    ])
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'title')
                    ->label('Category'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
        ->query(Book::withTrashed())
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),
                    SelectColumn::make('privacy')
                    ->options([
                        PrivacyEnum::PUBLIC->value => 'Public',
                        PrivacyEnum::PRIVATE->value => 'Private',
                        PrivacyEnum::FRIENDS->value => 'Friends',
                        PrivacyEnum::FRIENDS_OF_FRIENDS->value => 'Friends of Friends',
                    ])
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category.title')
                    ->label('Category')
                    ->sortable(),
                TextColumn::make('created_at')->label('Created At')->sortable(),

                    TextColumn::make('deleted_at')->label('Disabled')->sortable(),
            ])
            ->filters([
                // Add filters for different privacy statuses if needed
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('disable')
                    ->label('Disable')
                    ->icon('heroicon-o-x-circle') 
                    ->visible(fn($record) => $record->deleted_at === null)
                    ->action(function ($record) {
                        $record->delete();
                    })
                    ->requiresConfirmation()
                    ->color('danger'),

                Tables\Actions\Action::make('enable')
                    ->label('Enable')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn($record) => $record->deleted_at !== null)  
                    ->action(function ($record) {
                        $record->restore(); 
                    })
                    ->requiresConfirmation()
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
