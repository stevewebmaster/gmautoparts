<?php

namespace App\Filament\Resources\PartSubcategoryResource\Pages;

use App\Filament\Resources\PartSubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartSubcategory extends EditRecord
{
    protected static string $resource = PartSubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
