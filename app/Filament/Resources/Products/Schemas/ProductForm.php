<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Product Details')->schema([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    \Filament\Forms\Components\TextInput::make('price')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                    \Filament\Forms\Components\FileUpload::make('image')
                        ->image()
                        ->disk('public')
                        ->directory('products')
                        ->columnSpanFull(),
                    \Filament\Forms\Components\Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),

                \Filament\Schemas\Components\Section::make('Stats')->schema([
                    \Filament\Forms\Components\TextInput::make('rating')
                         ->numeric()
                         ->default(5.0)
                         ->maxValue(5.0)
                         ->step(0.1),
                    \Filament\Forms\Components\TextInput::make('sold_count')
                         ->numeric()
                         ->default(0)
                         ->label('Sold Count'),
                    \Filament\Forms\Components\Toggle::make('is_active')
                        ->default(true)
                        ->required(),
                ])->columns(3),
            ]);
    }
}
