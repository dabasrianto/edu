<?php

namespace App\Filament\Resources\ProductMessages;

use App\Filament\Resources\ProductMessages\Pages\CreateProductMessage;
use App\Filament\Resources\ProductMessages\Pages\EditProductMessage;
use App\Filament\Resources\ProductMessages\Pages\ListProductMessages;
use App\Filament\Resources\ProductMessages\Schemas\ProductMessageForm;
use App\Filament\Resources\ProductMessages\Tables\ProductMessagesTable;
use App\Models\ProductMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductMessageResource extends Resource
{
    protected static ?string $model = ProductMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Pesan Masuk';
    protected static string | \UnitEnum | null $navigationGroup = 'E-Commerce';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->disabled(),
                \Filament\Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->disabled(),
                \Filament\Forms\Components\Textarea::make('message')
                    ->disabled()
                    ->columnSpanFull(),
                \Filament\Forms\Components\Textarea::make('reply')
                    ->label('Balas Pesan')
                    ->rows(3)
                    ->columnSpanFull(),
                \Filament\Forms\Components\Toggle::make('is_read')
                    ->label('Sudah Dibaca'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengirim')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('message')
                    ->limit(50)
                    ->label('Pesan'),
                \Filament\Tables\Columns\TextColumn::make('reply')
                    ->limit(50)
                    ->label('Balasan Admin'),
                \Filament\Tables\Columns\IconColumn::make('is_read')
                    ->boolean()
                    ->label('Dibaca'),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductMessages::route('/'),
            'create' => CreateProductMessage::route('/create'),
            'edit' => EditProductMessage::route('/{record}/edit'),
        ];
    }
}
