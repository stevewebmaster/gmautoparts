<?php

namespace App\Filament\Resources\SliderSlideResource\Pages;

use App\Filament\Resources\SliderSlideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSliderSlide extends EditRecord
{
    protected static string $resource = SliderSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
