<?php

namespace App\Filament\Resources\AiSettingResource\Pages;

use App\Filament\Resources\AiSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAiSettings extends ManageRecords
{
    protected static string $resource = AiSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
