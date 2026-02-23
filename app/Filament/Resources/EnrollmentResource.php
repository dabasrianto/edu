<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;
    
    // Static properties removed to avoid type conflict with methods

    // Must use methods to avoid type errors
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Grup Akademi';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() > 0 ? 'warning' : 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->disabled(),
            Forms\Components\Select::make('course_id')
                ->relationship('course', 'title')
                ->disabled(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'rejected' => 'Rejected',
                ])
                ->required(),
            Forms\Components\FileUpload::make('payment_proof')
                ->image()
                ->directory('payments')
                ->openable()
                ->downloadable()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable()->label('Student'),
                Tables\Columns\TextColumn::make('course.title')->searchable()->sortable()->label('Course'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'danger' => 'rejected',
                    ]),
                 Tables\Columns\ImageColumn::make('payment_proof')->label('Proof'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('approve')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->action(fn (Enrollment $record) => $record->update(['status' => 'active']))
                    ->visible(fn (Enrollment $record) => $record->status === 'pending'),
                \Filament\Actions\Action::make('reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->requiresConfirmation()
                    ->action(fn (Enrollment $record) => $record->update(['status' => 'rejected']))
                    ->visible(fn (Enrollment $record) => $record->status === 'pending'),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
