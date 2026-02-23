<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';
    protected static string | \UnitEnum | null $navigationGroup = 'E-Commerce';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Section::make('Order Details')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->disabled(), // Usually we don't change user of an order
                        TextInput::make('total_amount')
                            ->numeric()
                            ->required(),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        Textarea::make('shipping_address')
                            ->columnSpanFull(),
                        Textarea::make('admin_note')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Payment Proof')
                    ->schema([
                        FileUpload::make('payment_proof')
                            ->image()
                            ->directory('payment-proofs')
                            ->openable()
                            ->downloadable(),
                    ]),
                Forms\Components\Section::make('Items')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->disabled(),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->disabled(),
                                TextInput::make('price')
                                    ->numeric()
                                    ->label('Price at Purchase')
                                    ->disabled(),
                            ])
                            ->addable(false) // Admin shouldn't add items manually usually
                            ->deletable(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')->searchable()->label('Customer'),
                TextColumn::make('total_amount')->money('IDR')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'primary' => 'shipped',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                ImageColumn::make('payment_proof')->circular(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
