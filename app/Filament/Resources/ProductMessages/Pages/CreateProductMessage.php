<?php

namespace App\Filament\Resources\ProductMessages\Pages;

use App\Filament\Resources\ProductMessages\ProductMessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductMessage extends CreateRecord
{
    protected static string $resource = ProductMessageResource::class;
}
