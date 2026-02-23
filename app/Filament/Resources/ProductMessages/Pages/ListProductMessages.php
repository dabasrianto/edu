<?php

namespace App\Filament\Resources\ProductMessages\Pages;

use App\Filament\Resources\ProductMessages\ProductMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductMessages extends ListRecords
{
    protected static string $resource = ProductMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
