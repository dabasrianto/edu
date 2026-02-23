<?php

namespace App\Filament\Resources\QuizAttempts;

use App\Filament\Resources\QuizAttempts\Pages\CreateQuizAttempt;
use App\Filament\Resources\QuizAttempts\Pages\EditQuizAttempt;
use App\Filament\Resources\QuizAttempts\Pages\ListQuizAttempts;
use App\Filament\Resources\QuizAttempts\Schemas\QuizAttemptForm;
use App\Filament\Resources\QuizAttempts\Tables\QuizAttemptsTable;
use App\Models\QuizAttempt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QuizAttemptResource extends Resource
{
    protected static ?string $model = QuizAttempt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationGroup(): ?string
    {
        return 'Quiz';
    }

    public static function form(Schema $schema): Schema
    {
        return QuizAttemptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuizAttemptsTable::configure($table);
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
            'index' => ListQuizAttempts::route('/'),
            'create' => CreateQuizAttempt::route('/create'),
            'edit' => EditQuizAttempt::route('/{record}/edit'),
        ];
    }
}
