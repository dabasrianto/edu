<?php

namespace App\Filament\Resources\BankAccounts;

use App\Filament\Resources\BankAccounts\Pages;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-building-library';
    }

    public static function getNavigationLabel(): string
    {
        return 'Bank Accounts';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }



    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('bank_name')
                ->required()
                ->label('Nama Bank'),
            Forms\Components\TextInput::make('account_number')
                ->required()
                ->label('Nomor Rekening'),
            Forms\Components\TextInput::make('account_holder')
                ->required()
                ->label('Atas Nama'),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif?')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('bank_name')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('account_number')->searchable(),
            Tables\Columns\TextColumn::make('account_holder')->searchable(),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
        ])
        ->actions([
            \Filament\Actions\EditAction::make(),
        ])
        ->bulkActions([
             \Filament\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
