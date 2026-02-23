<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationLabel(): string
    {
        return 'Users';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }



    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('role')
                ->options([
                    'admin' => 'Admin',
                    'user' => 'User',
                    'ustad' => 'Ustad',
                ])
                ->default('user')
                ->required(),
            Forms\Components\TextInput::make('whatsapp')
                ->maxLength(20),
            Forms\Components\Textarea::make('address')
                ->rows(3),
            Forms\Components\Toggle::make('is_active')
                ->label('Active / Approved')
                ->default(false),
            Forms\Components\TextInput::make('password')
                ->password()
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn (string $context): bool => $context === 'create'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\BadgeColumn::make('role')
                ->colors([
                    'secondary' => 'user',
                    'primary' => 'ustad',
                    'danger' => 'admin',
                ]),
            Tables\Columns\IconColumn::make('is_active')
                ->boolean()
                ->label('Active'),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('role'),
            Tables\Filters\TernaryFilter::make('is_active')->label('Status'),
        ])
        ->actions([
            \Filament\Actions\EditAction::make(),
            
            // Approve Action
            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (User $record) => !$record->is_active)
                ->action(fn (User $record) => $record->update(['is_active' => true])),
                
            // Deactivate Action
            Action::make('deactivate')
                ->label('Deactivate')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (User $record) => $record->is_active)
                ->action(fn (User $record) => $record->update(['is_active' => false])),
        ])
        ->bulkActions([
            \Filament\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
