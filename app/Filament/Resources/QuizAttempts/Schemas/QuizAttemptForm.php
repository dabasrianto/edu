<?php

namespace App\Filament\Resources\QuizAttempts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuizAttemptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('quiz_id')
                    ->required()
                    ->numeric(),
                TextInput::make('score')
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('started'),
                DateTimePicker::make('started_at')
                    ->required(),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
