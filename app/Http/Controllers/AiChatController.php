<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiSetting;
use App\Services\AiService;

class AiChatController extends Controller
{
    public function getSettings()
    {
        // Get the first active setting
        $setting = AiSetting::where('is_active', true)->first();

        if (!$setting) {
            return response()->json([]);
        }

        return response()->json([
            'id' => $setting->id,
            'provider' => $this->getProviderName($setting->provider),
            // We only send the selected model string, not the list
            'model' => $setting->selected_model,
        ]);
    }

    protected function getProviderName(string $provider): string
    {
        return match($provider) {
            'gemini' => 'Google Gemini',
            'openai' => 'OpenAI',
            'groq' => 'Groq (Fast)',
            'qwen' => 'Qwen (Alibaba)',
            default => ucfirst($provider)
        };
    }

    public function chat(Request $request, AiService $service)
    {
        $request->validate([
            'setting_id' => 'required|exists:ai_settings,id',
            'model' => 'required|string',
            'message' => 'required|string',
            'history' => 'nullable|array'
        ]);

        $setting = AiSetting::findOrFail($request->setting_id);

        if (!$setting->is_active) {
            return response()->json(['error' => 'Model ini sedang tidak aktif.'], 403);
        }

        $history = $request->history ?? [];
        $messages = array_merge($history, [
            ['role' => 'user', 'content' => $request->message]
        ]);

        $systemPrompt = $setting->system_prompt;
        if ($setting->reference_url) {
            $systemPrompt .= "\n\nGunakan website berikut sebagai referensi instruksi tambahan: " . $setting->reference_url;
        }

        $response = $service->chat(
            $setting->provider,
            $setting->api_key,
            $setting->selected_model ?? $request->model,
            $messages,
            $systemPrompt
        );

        return response()->json([
            'response' => $response
        ]);
    }
}
