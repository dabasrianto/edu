<?php

namespace App\Filament\Pages;

use App\Models\AppSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput; 
use Filament\Pages\Concerns\InteractsWithFormActions;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithFormActions;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-cog-6-tooth';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public function getTitle(): string
    {
        return 'App Configuration';
    }

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = AppSetting::firstOrCreate(['key' => 'main_settings']);
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Identity')
                            ->schema([
                                Forms\Components\FileUpload::make('logo_path')
                                    ->label('Logo')
                                    ->image()
                                    ->disk('public')
                                    ->directory('logos'),
                                Forms\Components\FileUpload::make('favicon_path')
                                    ->label('Favicon')
                                    ->image()
                                    ->disk('public')
                                    ->directory('favicons'),
                                Forms\Components\TextInput::make('login_header_text')
                                    ->label('Login Page Header')
                                    ->default('Login Pengguna')
                                    ->helperText('Text shown on the login page header.'),
                                Forms\Components\TextInput::make('app_name')
                                    ->label('App Name')
                                    ->default('Edu HSI')
                                    ->helperText('App Name shown on login page.'),
                                Forms\Components\TextInput::make('app_slogan')
                                    ->label('App Slogan')
                                    ->default('Belajar Kapanpun, Dimanapun')
                                    ->helperText('App Slogan shown on login page.'),
                            ]),
                        Tabs\Tab::make('Appearance')
                            ->schema([
                                Forms\Components\Select::make('theme_color')
                                    ->options([
                                        'blue' => 'Blue',
                                        'red' => 'Red',
                                        'emerald' => 'Emerald',
                                        'purple' => 'Purple',
                                        'gray' => 'Grayscale',
                                        'orange' => 'Orange',
                                        'amber' => 'Amber',
                                        'teal' => 'Teal',
                                        'cyan' => 'Cyan',
                                        'indigo' => 'Indigo',
                                        'rose' => 'Rose',
                                        'pink' => 'Pink',
                                    ])
                                    ->required()
                                    ->default('blue'),
                                Forms\Components\Select::make('font_family')
                                    ->options([
                                        'Inter' => 'Inter',
                                        'Roboto' => 'Roboto',
                                        'Poppins' => 'Poppins',
                                        'Serif' => 'Serif',
                                    ])
                                    ->required()
                                    ->default('Inter'),
                            ]),
                        Tabs\Tab::make('Homepage')
                            ->schema([
                                Section::make('Main Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('home_config.greeting')
                                            ->label('Greeting / Sapaan')
                                            ->default('Assalamualaikum,'),
                                        Forms\Components\TextInput::make('home_config.posts_limit')
                                            ->label('Home Blog Limit')
                                            ->helperText('Number of posts to show on Homepage')
                                            ->numeric()
                                            ->default(5)
                                            ->minValue(1)
                                            ->maxValue(20),
                                        Forms\Components\TextInput::make('home_config.products_limit')
                                            ->label('Home Products Limit')
                                            ->helperText('Number of products to show on Homepage')
                                            ->numeric()
                                            ->default(6)
                                            ->minValue(1)
                                            ->maxValue(50),
                                    ])->columns(2),
                                Section::make('Info Pendaftaran (Registration Info)')
                                    ->schema([
                                        Forms\Components\Repeater::make('home_config.info_blocks')
                                            ->label('Daftar Info Pendaftaran')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Banner Title (Blue Box)')
                                                    ->default('PENDAFTARAN BARU'),
                                                Forms\Components\Textarea::make('body')
                                                    ->label('Content Body')
                                                    ->rows(2)
                                                    ->default('Deskripsi info pendaftaran...'),
                                                Forms\Components\TextInput::make('status')
                                                    ->label('Status / Footer Text')
                                                    ->default('KUOTA TERBATAS'),
                                                
                                                Grid::make(3)
                                                    ->schema([
                                                        Forms\Components\Select::make('action_type')
                                                            ->label('Action Type')
                                                            ->options([
                                                                'none' => 'None',
                                                                'tab_akademi' => 'Switch to Akademi Tab',
                                                                'tab_reguler' => 'Switch to Reguler Tab',
                                                                'url' => 'Open External URL',
                                                            ])
                                                            ->default('none'),
                                                        Forms\Components\TextInput::make('action_label')
                                                            ->label('Button Label')
                                                            ->default('Lihat Detail'),
                                                        Forms\Components\TextInput::make('action_url')
                                                            ->label('External URL')
                                                            ->visible(fn ($get) => $get('action_type') === 'url'),
                                                    ]),
                                            ])
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Info Block'),

                                        Forms\Components\TextInput::make('home_config.reg_info_title')
                                            ->label('Main Section Title')
                                            ->default('Info Pendaftaran'),
                                            
                                        Forms\Components\TextInput::make('home_config.bill_title')
                                            ->label('Billing Block Title')
                                            ->default('Tagihan Pembelajaran'),
                                    ]),
                            ]),
                        Tabs\Tab::make('Blog')
                            ->schema([
                                Forms\Components\TextInput::make('blog_title')
                                    ->label('Blog Page Title')
                                    ->default('Artikel Terbaru'),
                                Forms\Components\TextInput::make('blog_config.posts_limit')
                                    ->label('Blog Page Limit')
                                    ->helperText('Max number of posts to fetch for Blog Tab. Set high (e.g. 50) for "All".')
                                    ->numeric()
                                    ->default(20)
                                    ->minValue(1)
                                    ->maxValue(100),
                            ]),
                        Tabs\Tab::make('Academy')
                            ->schema([
                                Forms\Components\TextInput::make('academy_title')
                                    ->label('Academy Title')
                                    ->default('Akademi HSI')
                                    ->required(),
                                Forms\Components\TextInput::make('academy_slogan')
                                    ->label('Academy Slogan')
                                    ->default('Tuntutlah ilmu dari buaian hingga liang lahat.')
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Regular')
                            ->schema([
                                Forms\Components\TextInput::make('regular_title')
                                    ->label('Regular Program Title')
                                    ->default('Program Reguler')
                                    ->required(),
                                Forms\Components\TextInput::make('regular_slogan')
                                    ->label('Regular Program Slogan')
                                    ->default('Evaluasi pemahamanmu secara berkala.')
                                    ->required(),
                            ]),
                            Tabs\Tab::make('Payment')
                            ->schema([
                                Forms\Components\Textarea::make('payment_config.instruction_text')
                                    ->label('Instruksi Pembayaran')
                                    ->helperText('Teks yang muncul di modal pembayaran.')
                                    ->default('Silakan transfer sebesar nominal tagihan ke rekening admin, lalu upload bukti pembayarannya di sini.')
                                    ->rows(3),
                                Forms\Components\TextInput::make('payment_config.whatsapp_number')
                                    ->label('Nomor WhatsApp Admin')
                                    ->helperText('Nomor WA untuk konfirmasi (Format: 628...).')
                                    ->default('6281234567890'),
                            ]),
                        Tabs\Tab::make('Google Auth')
                            ->schema([
                                Forms\Components\Toggle::make('google_login_enabled')
                                    ->label('Enable Google Login')
                                    ->default(true)
                                    ->helperText('Turn on/off "Login with Google" button on the user login page.'),
                                Forms\Components\TextInput::make('google_client_id')
                                    ->label('Google Client ID')
                                    ->placeholder('xxxx-xxxx.apps.googleusercontent.com')
                                    ->password()
                                    ->revealable(true)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('google_client_secret')
                                    ->label('Google Client Secret')
                                    ->password()
                                    ->revealable(true)
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('callback_url')
                                    ->label('Callback URL')
                                    ->content(url('/auth/google/callback'))
                                    ->helperText('Use this URL in your Google Console credentials configuration.'),
                            ]),

                         Tabs\Tab::make('Menu')
                            ->schema([
                                Forms\Components\Repeater::make('menu_config')
                                    ->label('Bottom Navigation')
                                    ->schema([
                                        Forms\Components\TextInput::make('id')->required(),
                                        Forms\Components\TextInput::make('label')->required(),
                                        Forms\Components\TextInput::make('icon')->label('Heroicon Name (e.g. heroicon-o-home)'),
                                    ])
                                    ->collapsed()
                                    ->reorderable(true)
                                    ->reorderableWithButtons(true)
                                    ->cloneable(true)
                                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                                    ->columns(3),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    } 

    public function save(): void
    {
        $settings = AppSetting::firstOrCreate(['key' => 'main_settings']);
        $settings->update($this->form->getState());

        Notification::make()
            ->success()
            ->title('Settings saved successfully')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}
