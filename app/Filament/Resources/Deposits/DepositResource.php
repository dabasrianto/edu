<?php

namespace App\Filament\Resources\Deposits;

use App\Filament\Resources\Deposits\Pages;
use App\Models\Deposit;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class DepositResource extends Resource
{
    protected static ?string $model = Deposit::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationLabel(): string
    {
        return 'Deposits';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }
    
    // Disable creation from admin if we only want users to request
    public static function canCreate(): bool
    {
        return false;
    }



    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->disabled(),
            Forms\Components\Select::make('bank_account_id')
                ->relationship('bankAccount', 'bank_name')
                ->disabled(),
            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->prefix('IDR')
                ->disabled(),
            Forms\Components\TextInput::make('status')
                ->disabled(),
            Forms\Components\FileUpload::make('proof_image')
                ->image()
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label('Tanggal'),
            Tables\Columns\TextColumn::make('user.name')->searchable()->label('User'),
            Tables\Columns\TextColumn::make('bankAccount.bank_name')->label('Bank'),
            Tables\Columns\TextColumn::make('amount')->money('IDR')->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'approved',
                    'danger' => 'rejected',
                ]),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
        ])
        ->actions([
            // Approve Action
            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Deposit $record) => $record->status === 'pending')
                ->action(function (Deposit $record) {
                    \DB::transaction(function () use ($record) {
                         $record->update(['status' => 'approved']);
                         // Tambah saldo user
                         $record->user->increment('balance', $record->amount);
                    });
                }),
                
            // Reject Action
            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (Deposit $record) => $record->status === 'pending')
                ->action(function (Deposit $record) {
                    $record->update(['status' => 'rejected']);
                }),
        ])
        ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeposits::route('/'),
            // 'create' => Pages\CreateDeposit::route('/create'),
            'edit' => Pages\EditDeposit::route('/{record}/edit'),
        ];
    }
}
