<?php

namespace App\Filament\Resources\SliderSlideResource\Pages;

use App\Filament\Resources\SliderSlideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSliderSlides extends ListRecords
{
    protected static string $resource = SliderSlideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
