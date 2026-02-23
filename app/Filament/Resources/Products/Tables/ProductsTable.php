<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('image'),
                \Filament\Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                \Filament\Tables\Columns\TextColumn::make('price')->money('IDR')->sortable(),
                \Filament\Tables\Columns\TextColumn::make('rating')->numeric(1)->sortable(),
                \Filament\Tables\Columns\TextColumn::make('sold_count')->label('Sold'),
                \Filament\Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
