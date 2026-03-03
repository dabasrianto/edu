<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Messages\AssistantMessage;
use function Laravel\Ai\agent;

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
        // Set API Key dynamically for the provider
        config(["ai.providers.{$provider}.key" => $apiKey]);

        // Extract history and the last user prompt
        $history = [];
        $lastMessage = '';

        if (empty($messages)) {
            return 'No messages provided.';
        }

        // Assume the last message is the new prompt from the user
        $lastItem = array_pop($messages);
        if ($lastItem['role'] === 'user') {
            $lastMessage = $lastItem['content'];
        } else {
            // If the last message is not from user, it might be a continuation or error.
            // For now, treat it as the prompt if it has content.
            $lastMessage = $lastItem['content'];
        }

        // Build history objects
        foreach ($messages as $msg) {
            if ($msg['role'] === 'user') {
                $history[] = new UserMessage($msg['content']);
            } elseif (in_array($msg['role'], ['assistant', 'model'])) {
                $history[] = new AssistantMessage($msg['content']);
            }
        }

        try {
            $response = agent(
                instructions: $systemPrompt ?? 'You are a helpful assistant.',
                messages: $history
            )->prompt(
                prompt: $lastMessage,
                provider: $provider,
                model: $model
            );

            return $response->text;
        } catch (\Exception $e) {
            \Log::error("AI SDK Error ({$provider}): " . $e->getMessage());
            return "Error: " . $e->getMessage();
        }
    }


}
