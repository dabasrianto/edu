<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-photo';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\FileUpload::make('image')
                ->image()
                ->required()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('title')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('subtitle')
                ->maxLength(255),
            Forms\Components\Textarea::make('content')
                ->rows(3)
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('subtitle')->limit(30),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
