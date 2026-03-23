<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers\PartsRelationManager;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationGroup = 'Catalogue';

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $recordTitleAttribute = 'make';

    public static function getModelLabel(): string
    {
        return 'Now Dismantling Vehicle';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Now Dismantling';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Vehicle details')->schema([
                    Forms\Components\TextInput::make('make')->required()->maxLength(255),
                    Forms\Components\TextInput::make('model')->required()->maxLength(255),
                    Forms\Components\TextInput::make('year')->maxLength(255),
                    Forms\Components\TextInput::make('engine')->maxLength(255),
                    Forms\Components\TextInput::make('transmission')->maxLength(255),
                    Forms\Components\TextInput::make('stock_number')->maxLength(255),
                    Forms\Components\Textarea::make('notes')->rows(3),
                ])->columns(2),
                Forms\Components\Section::make('Images & visibility')->schema([
                    Forms\Components\FileUpload::make('images')
                        ->image()
                        ->multiple()
                        ->directory('vehicles')
                        ->disk('public')
                        ->reorderable()
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_visible')->default(true),
                ]),
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
                Tables\Columns\TextColumn::make('make')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('model')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('year')->sortable(),
                Tables\Columns\TextColumn::make('stock_number'),
                Tables\Columns\IconColumn::make('is_visible')->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible'),
            ])
            ->actions(Tables\Actions\EditAction::make())
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getRelations(): array
    {
        return [
            PartsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
