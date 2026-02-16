<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\BillingLog;
use App\Models\BillingRecord;
use App\Models\Clinic;
use App\Models\ClinicWhatsappSetting;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Plan;
use App\Models\SmsLog;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppUsage;
use App\Models\WhatsappLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SystemDemoSeeder extends Seeder
{
    private array $pakistaniNames = [
        'male' => [
            'Ahmed Khan',
            'Bilal Hussain',
            'Faisal Raza',
            'Hassan Ali',
            'Imran Malik',
            'Junaid Akhtar',
            'Kamran Shah',
            'Muhammad Usman',
            'Nadeem Aslam',
            'Omar Farooq',
            'Qasim Javed',
            'Rashid Mehmood',
            'Salman Tariq',
            'Tariq Aziz',
            'Waqas Nawaz',
            'Zahid Iqbal',
            'Arslan Butt',
            'Danish Mirza',
            'Ehsan Chaudhry',
            'Ghulam Abbas',
        ],
        'female' => [
            'Ayesha Siddiqui',
            'Bushra Naz',
            'Fatima Zahra',
            'Hina Parveen',
            'Iqra Sheikh',
            'Kiran Bashir',
            'Lubna Rani',
            'Maryam Bibi',
            'Naila Farooqi',
            'Rabia Sultana',
            'Sana Akram',
            'Tasleem Akhtar',
            'Uzma Gillani',
            'Wardah Naeem',
            'Zainab Bukhari',
            'Amina Yousaf',
            'Dania Saeed',
            'Farah Deeba',
            'Gulshan Ara',
            'Javeria Waheed',
        ],
    ];

    private array $specializations = [
        'General Physician',
        'Cardiologist',
        'Dermatologist',
        'ENT Specialist',
        'Gynecologist',
        'Neurologist',
        'Ophthalmologist',
        'Orthopedic Surgeon',
        'Pediatrician',
        'Psychiatrist',
        'Pulmonologist',
        'Urologist',
    ];

    private array $appointmentNotes = [
        'Regular checkup',
        'Follow-up visit',
        'Complaint of chest pain',
        'Skin rash examination',
        'Ear infection treatment',
        'Prenatal checkup',
        'Headache and dizziness',
        'Eye examination',
        'Knee pain consultation',
        'Counseling session',
        'Breathing difficulty',
        'UTI symptoms',
        'Blood pressure monitoring',
        'Diabetes management',
        'Post-surgery follow-up',
        'Vaccination',
        'Lab results review',
        'Prescription renewal',
        'Annual physical exam',
    ];

    public function run(): void
    {
        $this->command->info('🏥 Starting System Demo Seeder...');

        // Ensure plans exist
        $this->call(PlanSeeder::class);

        $plans = Plan::all();
        $testingPlan = $plans->firstWhere('slug', 'testing') ?? $plans->first();
        $basicPlan = $plans->firstWhere('slug', 'basic') ?? $plans->first();
        $proPlan = $plans->firstWhere('slug', 'pro') ?? $plans->first();

        // ─── Super Admin ───
        $superAdmin = User::firstOrCreate(
            ['email' => 'super@admin.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPER_ADMIN,
            ]
        );
        $this->command->info('✅ Super Admin: super@admin.com / password');

        // ─── Clinics ───
        $clinicsData = [
            [
                'name' => 'Shifa International Clinic',
                'phone' => '051-846-1234',
                'address' => 'Sector H-8/4, Islamabad',
                'opening_time' => '08:00:00',
                'closing_time' => '22:00:00',
                'plan' => $proPlan,
                'admin_email' => 'admin@shifa.com',
                'admin_name' => 'Dr. Rashid Ahmed',
            ],
            [
                'name' => 'Medicare Family Clinic',
                'phone' => '042-3578-9012',
                'address' => 'DHA Phase 5, Lahore',
                'opening_time' => '09:00:00',
                'closing_time' => '21:00:00',
                'plan' => $basicPlan,
                'admin_email' => 'admin@medicare.pk',
                'admin_name' => 'Dr. Farhan Saeed',
            ],
            [
                'name' => 'Al-Mustafa Health Center',
                'phone' => '021-3456-7890',
                'address' => 'Clifton Block 9, Karachi',
                'opening_time' => '08:30:00',
                'closing_time' => '20:30:00',
                'plan' => $testingPlan,
                'admin_email' => 'admin@almustafa.pk',
                'admin_name' => 'Dr. Amina Yousaf',
            ],
        ];

        foreach ($clinicsData as $clinicInfo) {
            $this->command->info("  🏥 Seeding clinic: {$clinicInfo['name']}");
            $this->seedClinic($clinicInfo, $superAdmin);
        }

        $this->command->newLine();
        $this->command->info('🎉 System Demo Seeder completed successfully!');
        $this->command->info('───────────────────────────────────────────');
        $this->command->info('Login Credentials (all passwords: "password"):');
        $this->command->info('  Super Admin : super@admin.com');
        $this->command->info('  Clinic Admin: admin@shifa.com');
        $this->command->info('  Clinic Admin: admin@medicare.pk');
        $this->command->info('  Clinic Admin: admin@almustafa.pk');
        $this->command->info('───────────────────────────────────────────');
    }

    private function seedClinic(array $info, User $superAdmin): void
    {
        $clinic = Clinic::firstOrCreate(
            ['name' => $info['name']],
            [
                'phone' => $info['phone'],
                'address' => $info['address'],
                'opening_time' => $info['opening_time'],
                'closing_time' => $info['closing_time'],
                'is_active' => true,
                'plan_id' => $info['plan']->id,
                'subscription_status' => 'active',
            ]
        );

        // ─── Clinic Admin ───
        $clinicAdmin = User::firstOrCreate(
            ['email' => $info['admin_email']],
            [
                'clinic_id' => $clinic->id,
                'name' => $info['admin_name'],
                'password' => Hash::make('password'),
                'role' => User::ROLE_CLINIC_ADMIN,
                'phone' => '0300' . rand(1000000, 9999999),
            ]
        );

        // ─── Receptionist ───
        $recepName = $this->randomName('female');
        User::firstOrCreate(
            ['email' => 'reception@' . Str::slug($clinic->name) . '.pk'],
            [
                'clinic_id' => $clinic->id,
                'name' => $recepName,
                'password' => Hash::make('password'),
                'role' => User::ROLE_RECEPTIONIST,
                'phone' => '0300' . rand(1000000, 9999999),
            ]
        );

        // ─── WhatsApp Settings ───
        ClinicWhatsappSetting::firstOrCreate(
            ['clinic_id' => $clinic->id],
            [
                'phone_number_id' => 'PN_' . strtoupper(Str::random(10)),
                'access_token' => 'demo_token_' . Str::random(20),
                'default_template' => 'appointment_reminder',
                'message_type' => rand(0, 1) ? 'template' : 'text',
                'custom_message' => "Assalam o Alaikum {patient_name},\n\nThis is a reminder for your appointment at {clinic_name} with {doctor_name}.\n📅 Date: {date}\n⏰ Time: {time}\n\nPlease reply YES to confirm or NO to cancel.\n\nThank you!",
                'reminder_hours_before' => collect([2, 4, 12, 24])->random(),
                'is_active' => true,
            ]
        );

        // ─── Doctors (3-4 per clinic) ───
        $numDoctors = rand(3, 4);
        $doctors = [];
        $usedSpecs = [];

        for ($i = 0; $i < $numDoctors; $i++) {
            $gender = rand(0, 1) ? 'male' : 'female';
            $name = 'Dr. ' . $this->randomName($gender);
            $spec = $this->randomSpec($usedSpecs);
            $usedSpecs[] = $spec;
            $email = strtolower(Str::slug(str_replace('Dr. ', '', $name), '.')) . '@' . Str::slug($clinic->name) . '.pk';
            $phone = '0321' . rand(1000000, 9999999);

            $docUser = User::firstOrCreate(
                ['email' => $email],
                [
                    'clinic_id' => $clinic->id,
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_DOCTOR,
                    'phone' => $phone,
                ]
            );

            $doctor = Doctor::firstOrCreate(
                ['user_id' => $docUser->id],
                [
                    'clinic_id' => $clinic->id,
                    'name' => $name,
                    'specialization' => $spec,
                    'phone' => $phone,
                    'email' => $email,
                    'consultation_fee' => collect([1000, 1500, 2000, 2500, 3000, 3500, 5000])->random(),
                    'is_available' => true,
                ]
            );
            $doctors[] = $doctor;
        }

        // ─── Patients (15-25 per clinic) ───
        $numPatients = rand(15, 25);
        $patients = [];

        for ($i = 0; $i < $numPatients; $i++) {
            $gender = rand(0, 1) ? 'male' : 'female';
            $name = $this->randomName($gender);
            $phone = '03' . collect(['00', '01', '02', '03', '11', '21', '31', '32', '33'])->random() . rand(1000000, 9999999);

            $patient = Patient::create([
                'clinic_id' => $clinic->id,
                'name' => $name,
                'phone' => $phone,
                'email' => strtolower(Str::slug($name, '.')) . rand(1, 99) . '@gmail.com',
                'gender' => $gender,
                'date_of_birth' => Carbon::now()->subYears(rand(5, 75))->subDays(rand(0, 365)),
                'address' => $this->randomAddress(),
            ]);
            $patients[] = $patient;
        }

        // ─── Appointments (past 30 days + today + next 7 days) ───
        $appointments = [];

        // Past appointments
        for ($dayOffset = 30; $dayOffset >= 1; $dayOffset--) {
            $date = Carbon::today()->subDays($dayOffset);
            if ($date->isWeekend())
                continue; // Skip weekends

            $numAppointments = rand(4, 8);
            for ($j = 0; $j < $numAppointments; $j++) {
                $hour = rand(9, 18);
                $minute = collect([0, 15, 30, 45])->random();
                $status = collect(['completed', 'completed', 'completed', 'cancelled', 'no_show'])->random();

                $appt = Appointment::create([
                    'clinic_id' => $clinic->id,
                    'patient_id' => $patients[array_rand($patients)]->id,
                    'doctor_id' => $doctors[array_rand($doctors)]->id,
                    'appointment_date' => $date->format('Y-m-d'),
                    'appointment_time' => sprintf('%02d:%02d:00', $hour, $minute),
                    'status' => $status,
                    'notes' => $this->appointmentNotes[array_rand($this->appointmentNotes)],
                    'cancellation_reason' => $status === 'cancelled' ? collect(['Patient requested', 'Doctor unavailable', 'Rescheduled', 'Emergency'])->random() : null,
                ]);
                $appointments[] = $appt;
            }
        }

        // Today's appointments
        for ($j = 0; $j < rand(6, 10); $j++) {
            $hour = rand(9, 18);
            $minute = collect([0, 15, 30, 45])->random();
            $isPast = $hour < (int) now()->format('H');
            $status = $isPast ? collect(['completed', 'completed', 'no_show'])->random() : 'booked';

            $appt = Appointment::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patients[array_rand($patients)]->id,
                'doctor_id' => $doctors[array_rand($doctors)]->id,
                'appointment_date' => Carbon::today()->format('Y-m-d'),
                'appointment_time' => sprintf('%02d:%02d:00', $hour, $minute),
                'status' => $status,
                'notes' => $this->appointmentNotes[array_rand($this->appointmentNotes)],
            ]);
            $appointments[] = $appt;
        }

        // Future appointments
        for ($dayOffset = 1; $dayOffset <= 7; $dayOffset++) {
            $date = Carbon::today()->addDays($dayOffset);
            if ($date->isWeekend())
                continue;

            $numAppointments = rand(3, 6);
            for ($j = 0; $j < $numAppointments; $j++) {
                $hour = rand(9, 18);
                $minute = collect([0, 15, 30, 45])->random();

                $appt = Appointment::create([
                    'clinic_id' => $clinic->id,
                    'patient_id' => $patients[array_rand($patients)]->id,
                    'doctor_id' => $doctors[array_rand($doctors)]->id,
                    'appointment_date' => $date->format('Y-m-d'),
                    'appointment_time' => sprintf('%02d:%02d:00', $hour, $minute),
                    'status' => 'booked',
                    'notes' => $this->appointmentNotes[array_rand($this->appointmentNotes)],
                ]);
                $appointments[] = $appt;
            }
        }

        // ─── WhatsApp Conversations & Messages ───
        $this->seedWhatsAppData($clinic, $patients, $appointments);

        // ─── WhatsApp Logs (Legacy) ───
        $this->seedWhatsAppLogs($clinic, $patients, $appointments);

        // ─── SMS Logs ───
        $this->seedSmsLogs($clinic, $patients);

        // ─── Activity Logs ───
        $this->seedActivityLogs($clinic, $clinicAdmin, $patients, $appointments, $doctors);

        // ─── Billing Records ───
        $this->seedBillingData($clinic, $info['plan']);

        // ─── WhatsApp Usage Stats ───
        $this->seedUsageStats($clinic);
    }

    private function seedWhatsAppData(Clinic $clinic, array $patients, array $appointments): void
    {
        $statuses = ['sent', 'delivered', 'read', 'failed'];
        $templateMessages = [
            "Assalam o Alaikum! Your appointment at {clinic} is confirmed for tomorrow. Please arrive 15 minutes early.",
            "Reminder: You have an appointment with Dr. {doctor} tomorrow at {time}. Reply YES to confirm.",
            "Dear {patient}, your lab results are ready. Please visit the clinic at your earliest convenience.",
            "Thank you for visiting {clinic}! We hope you had a great experience. Rate us: ⭐⭐⭐⭐⭐",
        ];
        $incomingMessages = [
            "YES",
            "Ok confirmed",
            "Thank you",
            "Can I reschedule?",
            "What time is my appointment?",
            "Is the doctor available today?",
            "I need to cancel",
            "What are your charges?",
            "Shukriya",
            "Ji",
        ];

        // Create 8-15 conversations
        $numConversations = rand(8, 15);
        for ($i = 0; $i < $numConversations; $i++) {
            $patient = $patients[array_rand($patients)];
            $startDate = Carbon::now()->subDays(rand(0, 25))->subHours(rand(0, 12));
            $conversationId = 'conv_' . Str::random(20);

            $conversation = WhatsAppConversation::create([
                'clinic_id' => $clinic->id,
                'conversation_id' => $conversationId,
                'phone_number' => $patient->phone,
                'started_at' => $startDate,
                'expires_at' => $startDate->copy()->addHours(24),
                'last_message_at' => $startDate->copy()->addMinutes(rand(5, 120)),
                'type' => collect(['business_initiated', 'user_initiated'])->random(),
                'category' => collect(['utility', 'marketing', 'authentication'])->random(),
                'message_count' => rand(2, 8),
                'is_billable' => rand(0, 1),
                'cost' => rand(0, 1) ? round(rand(1, 15) * 0.01, 4) : 0,
                'currency' => 'USD',
            ]);

            // Create messages in this conversation
            $numMessages = rand(2, 6);
            $msgTime = $startDate->copy();

            for ($m = 0; $m < $numMessages; $m++) {
                $isOutgoing = $m === 0 || rand(0, 1);
                $status = $isOutgoing ? $statuses[array_rand($statuses)] : 'received';
                $msgTime->addMinutes(rand(1, 30));

                $body = $isOutgoing
                    ? str_replace(
                        ['{clinic}', '{patient}', '{doctor}', '{time}'],
                        [$clinic->name, $patient->name, 'Dr. Smith', '10:00 AM'],
                        $templateMessages[array_rand($templateMessages)]
                    )
                    : $incomingMessages[array_rand($incomingMessages)];

                WhatsAppMessage::create([
                    'clinic_id' => $clinic->id,
                    'message_id' => 'msg_' . Str::random(15),
                    'wamid' => 'wamid.' . Str::random(30),
                    'from' => $isOutgoing ? $clinic->phone : $patient->phone,
                    'to' => $isOutgoing ? $patient->phone : $clinic->phone,
                    'type' => $isOutgoing ? collect(['template', 'text'])->random() : 'text',
                    'direction' => $isOutgoing ? 'outgoing' : 'incoming',
                    'body' => $body,
                    'status' => $status,
                    'metadata' => $isOutgoing ? ['template_name' => 'appointment_reminder'] : null,
                    'conversation_id' => $conversationId,
                    'created_at' => $msgTime,
                    'updated_at' => $msgTime,
                ]);
            }
        }
    }

    private function seedWhatsAppLogs(Clinic $clinic, array $patients, array $appointments): void
    {
        $statuses = ['sent', 'failed', 'received'];

        // Create 20-40 logs
        $numLogs = rand(20, 40);
        for ($i = 0; $i < $numLogs; $i++) {
            $patient = $patients[array_rand($patients)];
            $status = $statuses[array_rand($statuses)];
            $isIncoming = $status === 'received';
            $appt = !empty($appointments) ? $appointments[array_rand($appointments)] : null;

            WhatsappLog::create([
                'clinic_id' => $clinic->id,
                'appointment_id' => $appt?->id,
                'direction' => $isIncoming ? 'incoming' : 'outgoing',
                'phone' => $patient->phone,
                'template_name' => !$isIncoming ? 'appointment_reminder' : null,
                'payload' => $isIncoming
                    ? ['text' => ['body' => collect(['Yes', 'Thank you', 'Ok', 'Confirmed', 'Cancel please'])->random()]]
                    : ['template' => 'appointment_reminder', 'language' => 'en'],
                'response' => $status !== 'failed'
                    ? ['messages' => [['id' => 'wamid.' . Str::random(20)]]]
                    : ['error' => ['message' => 'Invalid phone number', 'code' => 131009]],
                'status' => $status,
                'error_message' => $status === 'failed' ? 'Invalid phone number format' : null,
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);
        }
    }

    private function seedSmsLogs(Clinic $clinic, array $patients): void
    {
        $messages = [
            'Reminder: Your appointment is tomorrow at {time}. Reply C to confirm.',
            'Your prescription is ready for pickup at {clinic}.',
            'OTP: {otp} for your ClinicFlow verification.',
            'Thank you for visiting {clinic}. See you next time!',
        ];

        $numLogs = rand(10, 25);
        for ($i = 0; $i < $numLogs; $i++) {
            $patient = $patients[array_rand($patients)];
            $status = collect(['sent', 'delivered', 'failed'])->random();

            SmsLog::create([
                'clinic_id' => $clinic->id,
                'patient_id' => $patient->id,
                'phone_number' => $patient->phone,
                'message' => str_replace(
                    ['{time}', '{clinic}', '{otp}'],
                    ['10:00 AM', $clinic->name, rand(100000, 999999)],
                    $messages[array_rand($messages)]
                ),
                'status' => $status,
                'provider_sid' => 'SM' . Str::random(32),
                'provider_response' => $status === 'failed'
                    ? ['error_code' => 21211, 'error_message' => 'Invalid phone number']
                    : ['sid' => 'SM' . Str::random(32), 'status' => $status],
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 12)),
            ]);
        }
    }

    private function seedActivityLogs(Clinic $clinic, User $admin, array $patients, array $appointments, array $doctors): void
    {
        $actions = ['created', 'updated', 'deleted'];
        $browsers = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_2) Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) Mobile/15E148',
        ];

        // Log some patient activities
        foreach (array_slice($patients, 0, min(10, count($patients))) as $patient) {
            ActivityLog::create([
                'clinic_id' => $clinic->id,
                'user_id' => $admin->id,
                'action' => $actions[array_rand($actions)],
                'loggable_type' => Patient::class,
                'loggable_id' => $patient->id,
                'changes' => ['before' => ['name' => 'Old Name'], 'after' => ['name' => $patient->name]],
                'ip_address' => '192.168.1.' . rand(1, 254),
                'user_agent' => $browsers[array_rand($browsers)],
                'created_at' => Carbon::now()->subDays(rand(0, 20))->subHours(rand(0, 12)),
            ]);
        }

        // Log some appointment activities
        foreach (array_slice($appointments, 0, min(15, count($appointments))) as $appt) {
            ActivityLog::create([
                'clinic_id' => $clinic->id,
                'user_id' => $admin->id,
                'action' => collect(['created', 'updated', 'status_changed'])->random(),
                'loggable_type' => Appointment::class,
                'loggable_id' => $appt->id,
                'changes' => ['before' => ['status' => 'booked'], 'after' => ['status' => $appt->status]],
                'ip_address' => '192.168.1.' . rand(1, 254),
                'user_agent' => $browsers[array_rand($browsers)],
                'created_at' => $appt->created_at ?? Carbon::now()->subDays(rand(0, 20)),
            ]);
        }

        // Log some doctor activities
        foreach ($doctors as $doctor) {
            ActivityLog::create([
                'clinic_id' => $clinic->id,
                'user_id' => $admin->id,
                'action' => 'created',
                'loggable_type' => Doctor::class,
                'loggable_id' => $doctor->id,
                'changes' => ['after' => ['name' => $doctor->name, 'specialization' => $doctor->specialization]],
                'ip_address' => '192.168.1.' . rand(1, 254),
                'user_agent' => $browsers[array_rand($browsers)],
                'created_at' => Carbon::now()->subDays(rand(20, 45)),
            ]);
        }
    }

    private function seedBillingData(Clinic $clinic, Plan $plan): void
    {
        // Monthly billing records for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $billingDate = Carbon::now()->subMonths($i)->startOfMonth();
            $dueDate = $billingDate->copy()->addDays(15);
            $isPaid = $i > 0; // Current month unpaid

            BillingRecord::create([
                'clinic_id' => $clinic->id,
                'type' => 'monthly',
                'amount' => $plan->price,
                'billing_date' => $billingDate,
                'due_date' => $dueDate,
                'paid_date' => $isPaid ? $dueDate->copy()->subDays(rand(1, 5)) : null,
                'status' => $isPaid ? 'paid' : 'unpaid',
                'notes' => "{$plan->name} Plan - " . $billingDate->format('F Y'),
            ]);
        }

        // Billing logs (payment transactions)
        for ($i = 5; $i >= 1; $i--) {
            BillingLog::create([
                'clinic_id' => $clinic->id,
                'amount' => $plan->price,
                'payment_gateway' => 'stripe',
                'status' => 'successful',
                'transaction_id' => 'txn_' . Str::random(20),
                'plan_id' => $plan->id,
                'created_at' => Carbon::now()->subMonths($i)->startOfMonth()->addDays(rand(1, 10)),
            ]);
        }
    }

    private function seedUsageStats(Clinic $clinic): void
    {
        // Usage stats for last 3 months
        for ($i = 2; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            WhatsAppUsage::firstOrCreate(
                [
                    'clinic_id' => $clinic->id,
                    'month' => $month->month,
                    'year' => $month->year,
                ],
                [
                    'conversations_count' => rand(15, 80),
                    'messages_sent' => rand(50, 300),
                    'messages_delivered' => rand(40, 280),
                    'estimated_cost' => round(rand(50, 500) * 0.01, 2),
                    'currency' => 'USD',
                    'breakdown' => [
                        'utility' => rand(20, 60),
                        'marketing' => rand(5, 30),
                        'authentication' => rand(0, 10),
                    ],
                ]
            );
        }
    }

    // ─── Helpers ───

    private function randomName(string $gender): string
    {
        return $this->pakistaniNames[$gender][array_rand($this->pakistaniNames[$gender])];
    }

    private function randomSpec(array $used): string
    {
        $available = array_diff($this->specializations, $used);
        if (empty($available)) {
            return $this->specializations[array_rand($this->specializations)];
        }
        return $available[array_rand($available)];
    }

    private function randomAddress(): string
    {
        $areas = [
            'House 12, Street 5, G-10/2, Islamabad',
            'Flat 3B, Al-Noor Tower, DHA Phase 6, Lahore',
            'Plot 45, Block 2, PECHS, Karachi',
            'House 8, Lane 4, University Town, Peshawar',
            'Apartment 7, Gulshan-e-Iqbal, Block 5, Karachi',
            'House 23, Sector F, Bahria Town, Rawalpindi',
            'Flat 14, Askari Tower, Lahore Cantt',
            'House 56, Johar Town, Block J2, Lahore',
            'Plot 9, Street 12, I-8/1, Islamabad',
            'House 31, Model Town, Extension, Lahore',
        ];
        return $areas[array_rand($areas)];
    }
}
