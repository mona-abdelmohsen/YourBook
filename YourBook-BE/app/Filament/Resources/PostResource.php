<?php

namespace App\Filament\Resources;

use App\Enum\PrivacyEnum;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\Widgets\PostsPerDayChart;
use App\Filament\Resources\PostResource\Widgets\CommentsPerDayChart;
use App\Models\Posts\Post;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction; 
use Filament\Tables\Filters\SelectFilter;
class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getWidgets(): array
    {
        return [
            PostsPerDayChart::class,
            CommentsPerDayChart::class,
        ];
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('content')
                    ->label('Content')
                    ->required(),
                Select::make('privacy')
                    ->label('Privacy')
                    ->options([
                        PrivacyEnum::PUBLIC->value => 'Public',
                        PrivacyEnum::PRIVATE->value => 'Private',
                        PrivacyEnum::FRIENDS->value => 'Friends',
                        PrivacyEnum::FRIENDS_OF_FRIENDS->value => 'Friends of Friends',
                    ])
                    ->required(),
                Select::make('show_in_feed')
                    ->label('Show in Feed')
                    ->options([1 => 'Yes', 0 => 'No'])
                    ->required(),
                DateTimePicker::make('created_at')
                    ->label('Created At')
                    ->required(),
                TextInput::make('location')
                    ->label('Location')
                    ->nullable(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
        ->query(Post::withTrashed())
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('content')->sortable()->searchable(),
                SelectColumn::make('privacy')
                    ->options([
                        PrivacyEnum::PUBLIC->value => 'Public',
                        PrivacyEnum::PRIVATE->value => 'Private',
                        PrivacyEnum::FRIENDS->value => 'Friends',
                        PrivacyEnum::FRIENDS_OF_FRIENDS->value => 'Friends of Friends',
                    ])
                    ->sortable()
                    ->searchable(),
                BooleanColumn::make('show_in_feed')->sortable(),
                TextColumn::make('user.name')->label('User Name'),
                TextColumn::make('share_link')
                ->label('Share Link')
                ->formatStateUsing(fn ($state) => "<a href='{$state}' target='_blank' class='text-blue-500' rel='noopener noreferrer'>{$state}</a>")
                ->html() ,
                TextColumn::make('created_at')->label('Created At')->sortable(),
                TextColumn::make('deleted_at')->label('Disabled')->sortable(),
            ])
            ->filters([
                SelectFilter::make('privacy')
                    ->label('Privacy')
                    ->options([
                        PrivacyEnum::PUBLIC->value => 'Public',
                        PrivacyEnum::PRIVATE->value => 'Private',
                        PrivacyEnum::FRIENDS->value => 'Friends',
                        PrivacyEnum::FRIENDS_OF_FRIENDS->value => 'Friends of Friends',
                    ])
                    ->default(PrivacyEnum::PUBLIC->value),
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
            'index' => Pages\ListPosts::route('/'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
