<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialsRelationManager extends RelationManager
{
    protected static string $relationship = 'materials';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('type')
                ->options([
                    'video' => 'Video',
                    'text' => 'Artikel/Text',
                    'quiz' => 'Kuis',
                ])
                ->required()
                ->default('video'),
            Forms\Components\TextInput::make('duration')
                ->placeholder('e.g. 10 Menit'),
            Forms\Components\TextInput::make('media_url')
                ->label('Media URL (Video/Embed)')
                ->url(),
            Forms\Components\TextInput::make('timer_seconds')
                ->numeric()
                ->label('Timer (Detik)')
                ->default(0),
            Forms\Components\TextInput::make('order')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('order')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'video',
                        'success' => 'text',
                        'warning' => 'quiz',
                    ]),
                Tables\Columns\TextColumn::make('duration'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('order', 'asc');
    }
}
