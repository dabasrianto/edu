<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuizResource\Pages;
use App\Models\Quiz;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuizResource extends Resource
{
    protected static ?string $model = Quiz::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-rectangle-stack';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Quiz';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Wizard::make([
                    \Filament\Schemas\Components\Wizard\Step::make('Quiz Details')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Select::make('type')
                                ->options([
                                    'wajib' => 'Wajib',
                                    'sunnah' => 'Sunnah',
                                ])
                                ->required()
                                ->default('wajib'),
                            Forms\Components\TextInput::make('category')
                                ->placeholder('e.g., Silsilah Ilmiah 1'),
                            Forms\Components\TextInput::make('duration_minutes')
                                ->numeric()
                                ->default(10)
                                ->label('Duration (Minutes)'),
                            Forms\Components\Select::make('color')
                                ->options([
                                    'blue' => 'Blue',
                                    'green' => 'Green',
                                    'red' => 'Red',
                                    'yellow' => 'Yellow',
                                ])
                                ->default('blue'),
                            Forms\Components\Toggle::make('is_active')
                                ->default(true),
                            Forms\Components\Toggle::make('show_result')
                                ->label('Show Results to User')
                                ->default(false)
                                ->helperText('If enabled, users can see their score and detailed answers after submission.'),
                            Forms\Components\TextInput::make('certificate_threshold')
                                ->label('Certificate Passing Score')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->default(null)
                                ->placeholder('e.g., 70. Leave empty to disable certificate.')
                                ->helperText('Minimum score (0-100) required to download the certificate.'),
                            Forms\Components\Select::make('certificate_template_id')
                                ->label('Desain Sertifikat')
                                ->relationship('certificateTemplate', 'name')
                                ->preload()
                                ->searchable()
                                ->placeholder('Pilih Template (Opsional)')
                                ->helperText('Jika kosong, akan menggunakan template default yang sedang aktif.'),
                            Forms\Components\Textarea::make('description')
                                ->columnSpanFull(),
                        ]),
                    \Filament\Schemas\Components\Wizard\Step::make('Questions')
                        ->schema([
                            Forms\Components\Repeater::make('questions')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Textarea::make('question_text')
                                        ->required()
                                        ->label('Question')
                                        ->rows(2),
                                    Forms\Components\Select::make('type')
                                        ->options([
                                            'radio' => 'Single Choice (Radio)',
                                            'checkbox' => 'Multiple Choice (Checkbox)',
                                        ])
                                        ->default('radio')
                                        ->required(),
                                    Forms\Components\Hidden::make('order')
                                        ->default(0),
                                    
                                    Forms\Components\Repeater::make('options')
                                        ->relationship()
                                        ->schema([
                                            Forms\Components\TextInput::make('label')
                                                ->placeholder('A, B, C...')
                                                ->required(),
                                            Forms\Components\TextInput::make('text')
                                                ->required()
                                                ->label('Answer Text'),
                                            Forms\Components\Toggle::make('is_correct'),
                                        ])
                                        ->columns(3)
                                        ->label('Answers')
                                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? null),
                                ])
                                ->orderable('order')
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => strip_tags($state['question_text'] ?? null))
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->colors([
                        'primary' => 'wajib',
                        'success' => 'sunnah',
                    ]),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' mins'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('show_result')
                    ->label('Show Result'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListQuizzes::route('/'),
            'create' => Pages\CreateQuiz::route('/create'),
            'edit' => Pages\EditQuiz::route('/{record}/edit'),
        ];
    }
}
