<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('scrapeFromWp')
                ->label('Ambil dari WordPress')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Forms\Components\TextInput::make('wp_url')
                        ->label('URL WordPress')
                        ->placeholder('https://namasitus.com')
                        ->required()
                        ->url(),
                    Forms\Components\Select::make('category_id')
                        ->label('Kategori')
                        ->relationship('category', 'name')
                        ->required(),
                    Forms\Components\TextInput::make('limit')
                        ->label('Jumlah Artikel')
                        ->numeric()
                        ->default(10),
                ])
                ->action(function (array $data) {
                    try {
                        \Illuminate\Support\Facades\Artisan::call('app:scrape-wp', [
                            'url' => $data['wp_url'],
                            '--category_id' => $data['category_id'],
                            '--limit' => $data['limit'],
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Berhasil mengambil data')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Gagal mengambil data')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\CreateAction::make(),
        ];
    }
}
