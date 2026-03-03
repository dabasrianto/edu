<?php

namespace Tests\Feature;

use App\Models\AiSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Ai\Ai;
use Laravel\Ai\Agents\AnonymousAgent;

class AiChatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_settings_returns_active_setting()
    {
        AiSetting::create([
            'provider' => 'openai',
            'api_key' => 'test-key',
            'selected_model' => 'gpt-4o',
            'is_active' => true,
            'is_widget_active' => true,
            'system_prompt' => 'You are helpful.'
        ]);

        $response = $this->getJson(route('ai.settings'));

        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'provider', 'model']);
    }

    public function test_chat_returns_response()
    {
        $user = \App\Models\User::factory()->create();

        $setting = AiSetting::create([
            'provider' => 'openai',
            'api_key' => 'test-key',
            'selected_model' => 'gpt-4o',
            'is_active' => true,
            'is_widget_active' => true,
            'system_prompt' => 'You are helpful.'
        ]);

        Ai::fakeAgent(AnonymousAgent::class);

        $response = $this->actingAs($user)->postJson(route('ai.chat'), [
            'setting_id' => $setting->id,
            'model' => 'gpt-4o',
            'message' => 'Hello',
            'history' => []
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['response']);
    }

    public function test_widget_is_rendered_when_enabled()
    {
        AiSetting::create([
            'provider' => 'openai',
            'api_key' => 'test-key',
            'is_active' => true,
            'is_widget_active' => true,
        ]);

        // Need to share the variable manually or ensure the provider runs
        // In feature tests, providers run, so View::share should work if data exists
        
        $response = $this->get('/');
        // $response->assertSee('ai-chatbot');
        $this->assertStringContainsString('ai-chatbot', $response->getContent());
    }

    public function test_widget_is_not_rendered_when_disabled()
    {
        AiSetting::create([
            'provider' => 'openai',
            'api_key' => 'test-key',
            'is_active' => true,
            'is_widget_active' => false,
        ]);

        $response = $this->get('/');
        $response->assertDontSee('ai-chatbot');
    }
}
