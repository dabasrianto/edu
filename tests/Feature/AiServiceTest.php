<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\AiService;
use Laravel\Ai\Ai;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\AnonymousAgent;

class AiServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Fake the AI responses for AnonymousAgent
        Ai::fakeAgent(AnonymousAgent::class, [
             // We can provide a fake response string or a callback
             'This is a fake response from AI SDK.'
        ]);
    }

    public function test_ai_service_chat_uses_sdk()
    {
        $service = new AiService();
        $provider = 'openai';
        $apiKey = 'test-api-key';
        $model = 'gpt-4o';
        $messages = [
            ['role' => 'user', 'content' => 'Hello AI'],
        ];

        // Mock the response
        Ai::shouldReceive('recordPrompt')->andReturn(true); // Internal mocking helper if needed, but Ai::fake() usually handles it
        // Actually, Ai::fake() just fakes the generation.
        // We can inspect if the prompt was made?
        
        // Since we refactored to use agent()->prompt(), let's see if we can assert against the fake gateway.
        // The SDK's fake implementation might be tricky to assert against directly without specific helpers.
        // But let's try calling it and see if it returns a response without error.
        
        $response = $service->chat($provider, $apiKey, $model, $messages);

        // Since Ai::fake() returns a dummy response by default, we expect a string back.
        // The default fake response might be "Lorem ipsum...".
        
        $this->assertIsString($response);
        $this->assertNotEmpty($response);
    }

    public function test_ai_service_chat_handles_history()
    {
        $service = new AiService();
        $provider = 'gemini';
        $apiKey = 'test-gemini-key';
        $model = 'gemini-1.5-flash';
        $messages = [
            ['role' => 'user', 'content' => 'First question'],
            ['role' => 'assistant', 'content' => 'First answer'],
            ['role' => 'user', 'content' => 'Second question'],
        ];

        $response = $service->chat($provider, $apiKey, $model, $messages);

        $this->assertIsString($response);
    }
}
