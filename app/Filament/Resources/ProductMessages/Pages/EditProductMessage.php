<?php

namespace App\Filament\Resources\ProductMessages\Pages;

use App\Filament\Resources\ProductMessages\ProductMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductMessage extends EditRecord
{
    protected static string $resource = ProductMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['reply']) && empty($data['replied_at'])) {
            $data['replied_at'] = now();
        }

        return $data;
    }
}
