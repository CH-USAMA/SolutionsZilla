<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiExplorerController extends Controller
{
    /**
     * Display the API Explorer page.
     */
    public function index()
    {
        $endpoints = $this->getEndpoints();
        return view('super-admin.api-explorer', compact('endpoints'));
    }

    /**
     * Proxy an API request from the explorer UI.
     */
    public function execute(Request $request)
    {
        $method = strtoupper($request->input('method', 'GET'));
        $url = $request->input('url');
        $body = $request->input('body');

        // Parse body JSON if provided
        $parsedBody = [];
        if ($body) {
            $parsedBody = json_decode($body, true) ?? [];
        }

        // Build an internal request
        try {
            $internalRequest = Request::create(
                $url,
                $method,
                $parsedBody,
                $request->cookies->all(),
                [],
                $request->server->all(),
                $body
            );

            // Copy auth session
            $internalRequest->setLaravelSession($request->session());
            $internalRequest->setUserResolver(fn() => $request->user());

            // Dispatch
            $response = app()->handle($internalRequest);

            $responseBody = $response->getContent();
            $isJson = str_contains($response->headers->get('Content-Type', ''), 'json');

            return response()->json([
                'status' => $response->getStatusCode(),
                'headers' => collect($response->headers->all())->map(fn($v) => $v[0] ?? $v)->toArray(),
                'body' => $isJson ? json_decode($responseBody, true) : $responseBody,
                'is_json' => $isJson,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'headers' => [],
                'body' => ['error' => $e->getMessage()],
                'is_json' => true,
            ]);
        }
    }

    /**
     * Get all system API endpoints grouped by category.
     */
    private function getEndpoints(): array
    {
        return [
            [
                'category' => 'Health & System',
                'icon' => '💓',
                'endpoints' => [
                    [
                        'name' => 'Health Check',
                        'method' => 'GET',
                        'url' => '/health',
                        'description' => 'Check system health status (database, cache, queue).',
                        'params' => [],
                        'example_body' => null,
                    ],
                ],
            ],
            [
                'category' => 'WhatsApp Analytics',
                'icon' => '📊',
                'endpoints' => [
                    [
                        'name' => 'WhatsApp Stats',
                        'method' => 'GET',
                        'url' => '/api/whatsapp/stats',
                        'description' => 'Get WhatsApp usage statistics for a specific month/year. Super Admins can filter by clinic.',
                        'params' => [
                            ['name' => 'clinic_id', 'type' => 'integer', 'required' => false, 'description' => 'Filter by clinic ID (Super Admin only). Leave empty for collective stats.'],
                            ['name' => 'month', 'type' => 'integer', 'required' => false, 'description' => 'Month (1-12). Defaults to current month.'],
                            ['name' => 'year', 'type' => 'integer', 'required' => false, 'description' => 'Year. Defaults to current year.'],
                        ],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'WhatsApp Messages',
                        'method' => 'GET',
                        'url' => '/api/whatsapp/messages',
                        'description' => 'Get paginated WhatsApp message logs with filters.',
                        'params' => [
                            ['name' => 'clinic_id', 'type' => 'integer', 'required' => false, 'description' => 'Filter by clinic ID.'],
                            ['name' => 'status', 'type' => 'string', 'required' => false, 'description' => 'Filter by status: sent, delivered, read, failed.'],
                            ['name' => 'direction', 'type' => 'string', 'required' => false, 'description' => 'Filter by direction: outgoing, incoming.'],
                            ['name' => 'per_page', 'type' => 'integer', 'required' => false, 'description' => 'Results per page (default: 20).'],
                        ],
                        'example_body' => null,
                    ],
                ],
            ],
            [
                'category' => 'WhatsApp Webhook',
                'icon' => '🔔',
                'endpoints' => [
                    [
                        'name' => 'Webhook Verification (Subscribe)',
                        'method' => 'GET',
                        'url' => '/webhook/whatsapp',
                        'description' => 'Meta webhook verification challenge. Used when subscribing the webhook URL in the Meta App Dashboard.',
                        'params' => [
                            ['name' => 'hub_mode', 'type' => 'string', 'required' => true, 'description' => 'Must be "subscribe".'],
                            ['name' => 'hub_verify_token', 'type' => 'string', 'required' => true, 'description' => 'Your configured verify token.'],
                            ['name' => 'hub_challenge', 'type' => 'string', 'required' => true, 'description' => 'Random challenge string from Meta.'],
                        ],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'Webhook Incoming Event',
                        'method' => 'POST',
                        'url' => '/webhook/whatsapp',
                        'description' => 'Receive incoming WhatsApp events (messages, status updates). This is called by Meta Cloud API.',
                        'params' => [],
                        'example_body' => json_encode([
                            'entry' => [
                                [
                                    'id' => 'WHATSAPP_BUSINESS_ACCOUNT_ID',
                                    'changes' => [
                                        [
                                            'value' => [
                                                'messaging_product' => 'whatsapp',
                                                'metadata' => [
                                                    'display_phone_number' => '15551234567',
                                                    'phone_number_id' => 'PHONE_NUMBER_ID',
                                                ],
                                                'messages' => [
                                                    [
                                                        'from' => '923001234567',
                                                        'id' => 'wamid.EXAMPLE',
                                                        'timestamp' => (string) time(),
                                                        'text' => ['body' => 'Hello from patient!'],
                                                        'type' => 'text',
                                                    ]
                                                ],
                                            ],
                                            'field' => 'messages',
                                        ]
                                    ],
                                ]
                            ],
                        ], JSON_PRETTY_PRINT),
                    ],
                ],
            ],
            [
                'category' => 'Dashboard & Reports',
                'icon' => '📈',
                'endpoints' => [
                    [
                        'name' => 'Main Dashboard',
                        'method' => 'GET',
                        'url' => '/dashboard',
                        'description' => 'Get the main dashboard view (HTML page).',
                        'params' => [],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'Super Admin Dashboard',
                        'method' => 'GET',
                        'url' => '/admin/dashboard',
                        'description' => 'Get the Super Admin global dashboard (HTML page).',
                        'params' => [],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'WhatsApp Dashboard',
                        'method' => 'GET',
                        'url' => '/whatsapp/dashboard',
                        'description' => 'WhatsApp analytics dashboard with conversation stats and message logs.',
                        'params' => [
                            ['name' => 'clinic_id', 'type' => 'integer', 'required' => false, 'description' => 'Filter by clinic (Super Admin).'],
                            ['name' => 'month', 'type' => 'integer', 'required' => false, 'description' => 'Month filter.'],
                            ['name' => 'year', 'type' => 'integer', 'required' => false, 'description' => 'Year filter.'],
                        ],
                        'example_body' => null,
                    ],
                ],
            ],
            [
                'category' => 'Resource Management',
                'icon' => '🗂️',
                'endpoints' => [
                    [
                        'name' => 'List Doctors',
                        'method' => 'GET',
                        'url' => '/doctors',
                        'description' => 'List all doctors. Super Admin sees all clinics.',
                        'params' => [],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'List Patients',
                        'method' => 'GET',
                        'url' => '/patients',
                        'description' => 'List all patients. Super Admin sees all clinics.',
                        'params' => [],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'List Appointments',
                        'method' => 'GET',
                        'url' => '/appointments',
                        'description' => 'List all appointments. Super Admin sees all clinics.',
                        'params' => [],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'Reports',
                        'method' => 'GET',
                        'url' => '/reports',
                        'description' => 'View clinic reports and analytics.',
                        'params' => [],
                        'example_body' => null,
                    ],
                ],
            ],
            [
                'category' => 'Audit & Logs',
                'icon' => '📋',
                'endpoints' => [
                    [
                        'name' => 'Activity Logs (Clinic)',
                        'method' => 'GET',
                        'url' => '/admin/logs',
                        'description' => 'View clinic-specific activity/audit logs.',
                        'params' => [],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'Activity Logs (Global)',
                        'method' => 'GET',
                        'url' => '/super-admin/logs',
                        'description' => 'View all activity logs across all clinics (Super Admin only).',
                        'params' => [],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'WhatsApp Logs',
                        'method' => 'GET',
                        'url' => '/whatsapp/logs',
                        'description' => 'View WhatsApp communication logs. Super Admin can filter by clinic.',
                        'params' => [
                            ['name' => 'clinic_id', 'type' => 'integer', 'required' => false, 'description' => 'Filter by clinic (Super Admin).'],
                        ],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'SMS Logs',
                        'method' => 'GET',
                        'url' => '/sms/logs',
                        'description' => 'View SMS communication logs.',
                        'params' => [],
                        'example_body' => null,
                    ],
                ],
            ],
            [
                'category' => 'Clinic Management',
                'icon' => '🏥',
                'endpoints' => [
                    [
                        'name' => 'List Clinics',
                        'method' => 'GET',
                        'url' => '/super-admin/clinics',
                        'description' => 'View all registered clinics (Super Admin only).',
                        'params' => [],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'Create Clinic Form',
                        'method' => 'GET',
                        'url' => '/super-admin/clinics/create',
                        'description' => 'Show the form to create a new clinic and admin user.',
                        'params' => [],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'Manage Plans',
                        'method' => 'GET',
                        'url' => '/super-admin/plans',
                        'description' => 'View and manage subscription plans.',
                        'params' => [],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'User Management',
                        'method' => 'GET',
                        'url' => '/super-admin/users',
                        'description' => 'View all users across all clinics. Reset passwords.',
                        'params' => [],
                        'example_body' => null,
                    ],
                ],
            ],
            [
                'category' => 'Settings',
                'icon' => '⚙️',
                'endpoints' => [
                    [
                        'name' => 'WhatsApp Settings',
                        'method' => 'GET',
                        'url' => '/whatsapp/settings',
                        'description' => 'View/manage WhatsApp integration settings.',
                        'params' => [
                            ['name' => 'clinic_id', 'type' => 'integer', 'required' => false, 'description' => 'Select clinic to manage (Super Admin).'],
                        ],
                        'example_body' => null,
                    ],
                    [
                        'name' => 'Clinic Settings',
                        'method' => 'GET',
                        'url' => '/clinic/settings',
                        'description' => 'View/edit clinic profile and operating hours.',
                        'params' => [],
                        'example_body' => null,
                    ],
                ],
            ],
        ];
    }
}
