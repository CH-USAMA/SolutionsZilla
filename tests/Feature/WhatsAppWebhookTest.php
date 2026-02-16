<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\ClinicWhatsappSetting;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected $clinic;
    protected $settings;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a clinic and its settings
        $this->clinic = Clinic::factory()->create();
        $this->settings = ClinicWhatsappSetting::create([
            'clinic_id' => $this->clinic->id,
            'phone_number_id' => '1234567890',
            'waba_id' => 'waba_123',
            'display_phone_number' => '+1234567890',
            'access_token' => 'encrypted_token',
            'verify_token' => 'test_verify_token',
            'is_active' => true,
        ]);
    }

    public function test_validates_webhook_signature()
    {
        Config::set('services.whatsapp.app_secret', 'secret');

        $payload = json_encode(['object' => 'whatsapp_business_account']);
        $signature = hash_hmac('sha256', $payload, 'secret');

        $response = $this->post('/webhook/whatsapp', ['object' => 'whatsapp_business_account'], [
            'X-Hub-Signature-256' => 'sha256=' . $signature
        ]);

        // If signature is INVALID, it returns 401. 
        $response = $this->post('/webhook/whatsapp', ['object' => 'foo'], [
            'X-Hub-Signature-256' => 'sha256=invalid_signature'
        ]);

        $response->assertStatus(401);
    }

    public function test_processes_incoming_text_message()
    {
        // Mock valid signature or disable it for this test
        Config::set('services.whatsapp.app_secret', null);

        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'waba_123',
                    'changes' => [
                        [
                            'field' => 'messages',
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '1234567890',
                                    'phone_number_id' => '1234567890',
                                ],
                                'contacts' => [
                                    ['profile' => ['name' => 'John Doe'], 'wa_id' => '9876543210']
                                ],
                                'messages' => [
                                    [
                                        'from' => '9876543210',
                                        'id' => 'wamid.HBgLMTIzNDU2Nzg5MA==',
                                        'timestamp' => time(),
                                        'text' => ['body' => 'Hello World'],
                                        'type' => 'text',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->postJson('/webhook/whatsapp', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);

        // Assert Message Created
        $this->assertDatabaseHas('whatsapp_messages', [
            'clinic_id' => $this->clinic->id,
            'wamid' => 'wamid.HBgLMTIzNDU2Nzg5MA==',
            'body' => 'Hello World',
            'from' => '9876543210',
        ]);

        // Assert Conversation Created/Updated
        $this->assertDatabaseHas('whatsapp_conversations', [
            'clinic_id' => $this->clinic->id,
            'phone_number' => '9876543210',
        ]);
    }

    public function test_is_idempotent_for_duplicate_messages()
    {
        Config::set('services.whatsapp.app_secret', null);

        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'changes' => [
                        [
                            'field' => 'messages',
                            'value' => [
                                'metadata' => ['phone_number_id' => '1234567890'],
                                'messages' => [
                                    [
                                        'from' => '9876543210',
                                        'id' => 'wamid.UNIQUE_ID_123',
                                        'timestamp' => time(),
                                        'text' => ['body' => 'Duplicate Test'],
                                        'type' => 'text',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // First Request
        $this->postJson('/webhook/whatsapp', $payload)->assertStatus(200);

        // Second Request (Same ID)
        $this->postJson('/webhook/whatsapp', $payload)->assertStatus(200);

        // Should only be one message in DB
        $this->assertDatabaseCount('whatsapp_messages', 1);
    }
}
