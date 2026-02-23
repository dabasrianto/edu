<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class AiService
{
    public function getAvailableModels(string $provider, string $apiKey)
    {
        return match ($provider) {
            'gemini' => $this->getGeminiModels($apiKey),
            'openai' => $this->getOpenAiModels($apiKey),
            'groq' => $this->getGroqModels($apiKey),
            'qwen' => $this->getQwenModels($apiKey),
            default => [],
        };
    }

    protected function getGeminiModels(string $apiKey)
    {
        try {
            $pendingRequest = Http::asJson();
            if (app()->environment('local')) {
                $pendingRequest->withoutVerifying();
            }

            $response = $pendingRequest->get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
            
            if ($response->successful()) {
                return collect($response->json()['models'] ?? [])
                    ->filter(fn($m) => in_array('generateContent', $m['supportedGenerationMethods'] ?? []))
                    ->map(fn($m) => [
                        'id' => str_replace('models/', '', $m['name']),
                        'name' => $m['displayName'] ?? $m['name']
                    ])
                    ->values()
                    ->toArray();
            } else {
                \Log::error("Gemini API Status Error: " . $response->status() . " - " . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error("Gemini API Exception: " . $e->getMessage());
        }
        return [];
    }

    protected function getOpenAiModels(string $apiKey)
    {
        try {
            $pendingRequest = Http::withToken($apiKey);
            if (app()->environment('local')) {
                $pendingRequest->withoutVerifying();
            }

            $response = $pendingRequest->get("https://api.openai.com/v1/models");
            
            if ($response->successful()) {
                return collect($response->json()['data'] ?? [])
                    ->filter(fn($m) => str_contains($m['id'], 'gpt'))
                    ->map(fn($m) => [
                        'id' => $m['id'],
                        'name' => $m['id']
                    ])
                    ->values()
                    ->toArray();
            } else {
                \Log::error("OpenAI API Status Error: " . $response->status() . " - " . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error("OpenAI API Exception: " . $e->getMessage());
        }
        return [];
    }

    protected function getGroqModels(string $apiKey)
    {
        try {
            $pendingRequest = Http::withToken($apiKey);
            if (app()->environment('local')) {
                $pendingRequest->withoutVerifying();
            }

            $response = $pendingRequest->get("https://api.groq.com/openai/v1/models");
            
            if ($response->successful()) {
                return collect($response->json()['data'] ?? [])
                    ->map(fn($m) => [
                        'id' => $m['id'],
                        'name' => $m['id']
                    ])
                    ->values()
                    ->toArray();
            } else {
                \Log::error("Groq API Status Error: " . $response->status() . " - " . $response->body());
                Notification::make()->title('Groq Error')->body($response->body())->danger()->send();
            }
        } catch (\Exception $e) {
            \Log::error("Groq API Exception: " . $e->getMessage());
            Notification::make()->title('Groq Exception')->body($e->getMessage())->danger()->send();
        }
        return [];
    }

    protected function getQwenModels(string $apiKey)
    {
        // AliCloud DashScope models
        try {
            $response = Http::withToken($apiKey)->get("https://dashscope.aliyuncs.com/api/v1/models");
            if ($response->successful()) {
                 return collect($response->json()['data']['models'] ?? [])
                    ->map(fn($m) => [
                        'id' => $m['model_name'],
                        'name' => $m['model_name']
                    ])
                    ->values()
                    ->toArray();
            }
        } catch (\Exception $e) {
             \Log::error("Qwen API Error: " . $e->getMessage());
        }
        return [
            ['id' => 'qwen-turbo', 'name' => 'Qwen Turbo'],
            ['id' => 'qwen-plus', 'name' => 'Qwen Plus'],
            ['id' => 'qwen-max', 'name' => 'Qwen Max'],
        ];
    }
    
    public function chat(string $provider, string $apiKey, string $model, array $messages, ?string $systemPrompt = null)
    {
        return match ($provider) {
            'gemini' => $this->chatGemini($apiKey, $model, $messages, $systemPrompt),
            'openai', 'groq', 'qwen' => $this->chatOpenAiCompatible($provider, $apiKey, $model, $messages, $systemPrompt),
            default => 'Provider not supported.',
        };
    }

    protected function chatGemini(string $apiKey, string $model, array $messages, ?string $systemPrompt = null)
    {
        try {
            $contents = collect($messages)->map(fn($m) => [
                'role' => $m['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $m['content']]]
            ])->toArray();

            $payload = ['contents' => $contents];
            
            if ($systemPrompt) {
                $payload['system_instruction'] = [
                    'parts' => [['text' => $systemPrompt]]
                ];
            }

            $pendingRequest = Http::asJson();
            if (app()->environment('local')) {
                $pendingRequest->withoutVerifying();
            }

            $response = $pendingRequest->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", $payload);

            if ($response->successful()) {
                return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'No response from Gemini.';
            }
            
            \Log::error("Gemini Chat Error: " . $response->body());
            return "Gemini Error: " . ($response->json()['error']['message'] ?? $response->body());
        } catch (\Exception $e) {
            \Log::error("Gemini Chat Exception: " . $e->getMessage());
            return "Gemini Exception: " . $e->getMessage();
        }
    }

    protected function chatOpenAiCompatible(string $provider, string $apiKey, string $model, array $messages, ?string $systemPrompt = null)
    {
        $baseUrl = match($provider) {
            'openai' => 'https://api.openai.com/v1',
            'groq' => 'https://api.groq.com/openai/v1',
            'qwen' => 'https://dashscope.aliyuncs.com/compatible-mode/v1',
        };
        
        $finalMessages = $messages;
        if ($systemPrompt) {
            array_unshift($finalMessages, ['role' => 'system', 'content' => $systemPrompt]);
        }

        try {
            $pendingRequest = Http::withToken($apiKey);
            if (app()->environment('local')) {
                $pendingRequest->withoutVerifying();
            }

            $response = $pendingRequest->post("{$baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => $finalMessages,
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'] ?? 'No response.';
            }

            \Log::error("{$provider} Chat Error: " . $response->body());
            $error = $response->json();
            return "{$provider} Error: " . ($error['error']['message'] ?? $response->body());
        } catch (\Exception $e) {
            \Log::error("{$provider} Chat Exception: " . $e->getMessage());
            return "{$provider} Exception: " . $e->getMessage();
        }
    }
}
