<?php

namespace App\Filament\Resources\CertificateTemplates\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CertificateTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Template')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('background_image')
                    ->label('Gambar Background')
                    ->image()
                    ->directory('certificates/backgrounds')
                    ->required()
                    ->helperText('Upload gambar ukuran A4 Landscape (High Resolution).'),
                Toggle::make('is_active')
                    ->label('Aktifkan')
                    ->helperText('Template yang aktif akan digunakan secara default.')
                    ->default(false),
            ]);
    }
}
