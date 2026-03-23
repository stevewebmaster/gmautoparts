<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Unique identifier, e.g. about, contact'),
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\RichEditor::make('content')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('meta_description')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key'),
                Tables\Columns\TextColumn::make('title')->searchable(),
            ])
            ->filters([])
            ->actions(Tables\Actions\EditAction::make())
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
