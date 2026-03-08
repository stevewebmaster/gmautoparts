<?php

namespace App\Filament\Resources;

use App\Models\Part;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PartResource extends Resource
{
    protected static ?string $model = Part::class;

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Part details')->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('part_category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->reactive(),
                    Forms\Components\Select::make('part_subcategory_id')
                        ->relationship('subcategory', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\Textarea::make('description')->rows(4),
                    Forms\Components\TextInput::make('stock_number'),
                    Forms\Components\TextInput::make('condition'),
                    Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->prefix('$'),
                ])->columns(2),
                Forms\Components\Section::make('Vehicle compatibility')->schema([
                    Forms\Components\TextInput::make('make'),
                    Forms\Components\TextInput::make('model'),
                    Forms\Components\TextInput::make('year'),
                    Forms\Components\Select::make('vehicle_id')
                        ->relationship('vehicle', 'id')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                        ->searchable()
                        ->preload(),
                ])->columns(2)->collapsed(),
                Forms\Components\Section::make('Media & visibility')->schema([
                    Forms\Components\FileUpload::make('images')
                        ->image()
                        ->multiple()
                        ->directory('parts')
                        ->disk('public')
                        ->reorderable()
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_visible')->default(true),
                    Forms\Components\Toggle::make('is_featured')->default(false),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->stacked()
                    ->limit(1)
                    ->circular()
                    ->getStateUsing(fn ($record) => is_array($record->images) && count($record->images) ? $record->images[0] : null),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\TextColumn::make('stock_number'),
                Tables\Columns\TextColumn::make('price')->money('NZD')->sortable(),
                Tables\Columns\IconColumn::make('is_visible')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible'),
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions(Tables\Actions\EditAction::make())
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'stock_number', 'description'];
    }
}
