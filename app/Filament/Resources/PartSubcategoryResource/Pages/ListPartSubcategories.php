<?php

namespace App\Filament\Resources\PartSubcategoryResource\Pages;

use App\Filament\Resources\PartSubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPartSubcategories extends ListRecords
{
    protected static string $resource = PartSubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
