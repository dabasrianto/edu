<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Models\Course;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'ustad') {
            $query->where('teacher_id', auth()->id());
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            CourseResource\RelationManagers\MaterialsRelationManager::class,
        ];
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getNavigationLabel(): string
    {
        return 'Courses';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Grup Akademi';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255),
            Forms\Components\Textarea::make('short_desc')->rows(3),
            Forms\Components\Select::make('type')->options([
                'free' => 'Free',
                'paid' => 'Paid',
            ])->required(),
            Forms\Components\TextInput::make('price')->numeric()->minValue(0)->label('Price (IDR)')->hint('Leave empty for free'),
            Forms\Components\Select::make('color')->options([
                'blue' => 'Blue',
                'emerald' => 'Emerald',
                'orange' => 'Orange',
                'purple' => 'Purple',
                'red' => 'Red',
            ])->required(),

            // Teacher Assignment
            Forms\Components\Select::make('teacher_id')
                ->relationship('teacher', 'name')
                ->label('Instructor')
                ->visible(fn () => auth()->user()->role === 'admin')
                ->searchable()
                ->preload(),
            
            Forms\Components\Hidden::make('teacher_id')
                ->default(fn () => auth()->id())
                ->disabled(fn () => auth()->user()->role === 'admin'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('teacher.name')
                 ->label('Instructor')
                 ->sortable()
                 ->visible(auth()->user()->role === 'admin'),
            Tables\Columns\BadgeColumn::make('type')->colors([
                'success' => 'free',
                'warning' => 'paid',
            ]),
            Tables\Columns\TextColumn::make('price')->money('IDR')->sortable()->toggleable(),
            Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
        ])->actions([
            \Filament\Actions\EditAction::make(),
            \Filament\Actions\DeleteAction::make(),
        ])->bulkActions([
            \Filament\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}

