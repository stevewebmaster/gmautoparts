<?php

namespace App\Filament\Resources\VehicleResource\RelationManagers;

use App\Filament\Resources\PartResource;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PartsRelationManager extends RelationManager
{
    protected static string $relationship = 'parts';

    /** The same form as Catalogue → Parts, so a part added here is complete. */
    public function form(Form $form): Form
    {
        return PartResource::form($form);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('stock_number'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                    $vehicle = $this->getOwnerRecord();
                    $data['vehicle_id'] = $vehicle->id;

                    // The parts page filters by the part's own make, model and
                    // year, so fill any left blank from the vehicle.
                    foreach (['make', 'model', 'year'] as $field) {
                        if (blank($data[$field] ?? null)) {
                            $data[$field] = $vehicle->{$field};
                        }
                    }

                    return $data;
                }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
