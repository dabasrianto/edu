<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function getNavigationLabel(): string
    {
        return 'Artikel Blog';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'article');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                ->maxLength(255),

            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Forms\Components\Select::make('category_id')
                ->relationship('category', 'name')
                ->required()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\Select::make('color')
                        ->options([
                            'blue' => 'Blue',
                            'green' => 'Green',
                            'orange' => 'Orange',
                            'purple' => 'Purple',
                            'red' => 'Red',
                        ])
                        ->default('blue')
                        ->required(),
                ]),

            Forms\Components\FileUpload::make('image')
                ->image()
                ->directory('posts')
                ->columnSpanFull(),

            Forms\Components\RichEditor::make('content')
                ->required()
                ->columnSpanFull(),

            Forms\Components\Select::make('status')
                ->options([
                    'published' => 'Published',
                    'draft' => 'Draft',
                    'archived' => 'Archived',
                ])
                ->default('published')
                ->required(),
            
            Forms\Components\Toggle::make('show_in_slider')
                ->label('Tampilkan di Slider Beranda')
                ->default(false),
            
            Forms\Components\Hidden::make('type')
                ->default('article'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color(fn ($record) => $record->category->color ?? 'gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\ToggleColumn::make('show_in_slider')
                    ->label('Slider'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ]),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
