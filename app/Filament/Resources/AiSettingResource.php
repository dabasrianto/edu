<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiSettingResource\Pages\ManageAiSettings;
use App\Models\AiSetting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Actions\Action;
use App\Services\AiService;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AiSettingResource extends Resource
{
    protected static ?string $model = AiSetting::class;

    public static function getNavigationLabel(): string
    {
        return 'AI Chat Settings';
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-cpu-chip';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    protected static ?string $recordTitleAttribute = 'provider';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider')
                    ->options([
                        'gemini' => 'Google Gemini',
                        'openai' => 'OpenAI',
                        'groq' => 'Groq (Fast)',
                        'qwen' => 'Qwen (Alibaba)',
                    ])
                    ->required()
                    ->native(false)
                    ->live(),
                Select::make('selected_model')
                    ->label('Model Aktif')
                    ->options(fn (Get $get) => array_combine($get('models') ?? [], $get('models') ?? []))
                    ->placeholder('Sync Models dulu...')
                    ->required()
                    ->native(false),
                \Filament\Forms\Components\TextInput::make('api_key')
                    ->label('API/Secret Key')
                    ->required()
                    ->password()
                    ->revealable()
                    ->columnSpanFull()
                    ->suffixAction(
                        Action::make('sync_models_form')
                            ->icon('heroicon-m-arrow-path')
                            ->color('success')
                            ->action(function (Get $get, Set $set, AiService $service) {
                                $provider = $get('provider');
                                $apiKey = $get('api_key');
                                
                                if (!$provider || !$apiKey) {
                                    Notification::make()
                                        ->title('Provider dan API Key wajib diisi')
                                        ->warning()
                                        ->send();
                                    return;
                                }

                                $models = $service->getAvailableModels($provider, $apiKey);
                                
                                if (empty($models)) {
                                    Notification::make()
                                        ->title('Gagal sinkronisasi data')
                                        ->body('Pastikan API Key benar dan memiliki akses.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                $modelIds = collect($models)->pluck('id')->toArray();
                                $set('models', $modelIds);
                                
                                if (!empty($modelIds)) {
                                    $set('selected_model', $modelIds[0]);
                                }

                                Notification::make()
                                    ->title('Sinkronisasi Berhasil')
                                    ->body(count($modelIds) . ' model ditemukan.')
                                    ->success()
                                    ->send();
                            })
                    ),
                TagsInput::make('models')
                    ->placeholder('Klik ikon sinkronisasi di kolom API Key untuk mengisi otomatis')
                    ->columnSpanFull()
                    ->disabled()
                    ->dehydrated()
                    ->live(),
                Textarea::make('system_prompt')
                    ->label('Custom AI Instructions (Prompt)')
                    ->placeholder('Contoh: Anda adalah asisten edukasi HSI yang ramah...')
                    ->rows(4)
                    ->columnSpanFull(),
                \Filament\Forms\Components\TextInput::make('reference_url')
                    ->label('Reference Website (URL)')
                    ->placeholder('https://example.com/additional-instructions')
                    ->url()
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('provider'),
                TextEntry::make('api_key')
                    ->columnSpanFull(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('provider')
            ->columns([
                TextColumn::make('provider')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
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
                Action::make('sync_models')
                    ->label('Sync Models')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->action(function (AiSetting $record, AiService $service) {
                        $models = $service->getAvailableModels($record->provider, $record->api_key);
                        
                        if (empty($models)) {
                            Notification::make()
                                ->title('Gagal sinkronisasi data')
                                ->body('Pastikan API Key benar dan memiliki akses.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $modelIds = collect($models)->pluck('id')->toArray();
                        $data = ['models' => $modelIds];
                        
                        // Auto select first model if currently empty
                        if(empty($record->selected_model) && !empty($modelIds)) {
                            $data['selected_model'] = $modelIds[0];
                        }

                        $record->update($data);

                        Notification::make()
                            ->title('Sinkronisasi Berhasil')
                            ->body(count($modelIds) . ' model ditemukan dan disimpan.')
                            ->success()
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAiSettings::route('/'),
        ];
    }
}
