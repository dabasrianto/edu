<?php

namespace App\Filament\Auth;

use App\Models\AppSetting;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Auth\MultiFactor\Contracts\HasBeforeChallengeHook;
use Filament\Auth\MultiFactor\Contracts\MultiFactorAuthenticationProvider;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;

class EmailOtpProvider implements MultiFactorAuthenticationProvider, HasBeforeChallengeHook
{
    public function isEnabled(Authenticatable $user): bool
    {
        $settings = AppSetting::firstOrCreate(['key' => 'main_settings']);
        return (bool) ($settings->otp_config['otp_login'] ?? false);
    }

    public function getId(): string
    {
        return 'email-otp';
    }

    public function getLoginFormLabel(): string
    {
        return 'Email OTP';
    }

    /**
     * Track if OTP sending failed so we can skip the challenge.
     */
    public bool $sendFailed = false;

    /**
     * Called before the challenge form is shown — we generate and send the OTP here.
     */
    public function beforeChallenge(Authenticatable $user): void
    {
        if (! $user instanceof User) {
            return;
        }

        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        try {
            \App\Http\Controllers\EmailSettingController::applyDatabaseMailConfig();
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OTPMail($otp));
            $this->sendFailed = false;
        } catch (\Exception $e) {
            // OTP email failed — clear OTP and allow login without MFA
            $user->otp_code = null;
            $user->otp_expires_at = null;
            $user->save();
            $this->sendFailed = true;

            Notification::make()
                ->warning()
                ->title('OTP dilewati')
                ->body('Gagal mengirim email OTP. Login dilanjutkan tanpa OTP.')
                ->send();
        }
    }

    /**
     * @return array<Component | Action>
     */
    public function getManagementSchemaComponents(): array
    {
        // No user-managed settings for email OTP
        return [];
    }

    /**
     * @return array<Component | Action>
     */
    public function getChallengeFormComponents(Authenticatable $user): array
    {
        return [
            TextInput::make('code')
                ->label('Kode OTP')
                ->placeholder('Masukkan 6 digit kode OTP')
                ->helperText('Kode OTP telah dikirim ke email Anda. Berlaku 10 menit.')
                ->required()
                ->maxLength(6)
                ->minLength(6)
                ->numeric()
                ->autofocus()
                ->extraInputAttributes([
                    'style' => 'text-align: center; font-size: 1.5rem; letter-spacing: 0.5em; font-weight: bold;',
                ])
                ->rule(function () use ($user) {
                    return function (string $attribute, $value, $fail) use ($user) {
                        $freshUser = User::find($user->getAuthIdentifier());

                        if (!$freshUser) {
                            $fail('User tidak ditemukan.');
                            return;
                        }

                        if ($freshUser->otp_code !== $value) {
                            $fail('Kode OTP salah.');
                            return;
                        }

                        if (Carbon::now()->gt($freshUser->otp_expires_at)) {
                            $fail('Kode OTP telah kedaluwarsa. Silakan login ulang.');
                            return;
                        }

                        // Clear OTP after successful validation
                        $freshUser->otp_code = null;
                        $freshUser->otp_expires_at = null;
                        $freshUser->save();
                    };
                }),
        ];
    }
}
