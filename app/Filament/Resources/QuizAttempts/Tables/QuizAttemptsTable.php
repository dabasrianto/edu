<?php

namespace App\Filament\Resources\QuizAttempts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuizAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Peserta')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quiz.title')
                    ->label('Kuis')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('score')
                    ->label('Nilai')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'started' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('amount') // Just in case we added amount later? No.
                    ->hidden(),
                TextColumn::make('completed_at')
                    ->label('Selesai')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
