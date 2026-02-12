<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'stripe_price_id' => null,
                'price' => 0,
                'description' => 'Perfect for small clinics starting out.',
                'features' => ['Basic Dashboard', 'WhatsApp Reminders', 'SMS Reminders', 'Up to 50 Appointments/mo'],
                'max_users' => 2,
                'max_appointments' => 50,
                'max_whatsapp_messages' => 50,
                'max_sms_messages' => 10,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'stripe_price_id' => 'price_H5ggL4q6sL4q6s', // Placeholder
                'price' => 10,
                'description' => 'Growing clinics needing more capacity.',
                'features' => ['Priority Support', 'Urdu Communication Templates', 'Up to 300 Appointments/mo'],
                'max_users' => 5,
                'max_appointments' => 300,
                'max_whatsapp_messages' => 300,
                'max_sms_messages' => 100,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'stripe_price_id' => 'price_H5ggL4q7sL4q7s', // Placeholder
                'price' => 30,
                'description' => 'Full-scale clinics with high patient volume.',
                'features' => ['Unlimited Appointments', 'Unlimited Multi-Channel Messaging', 'Premium Reporting'],
                'max_users' => 15,
                'max_appointments' => 0, // 0 = Unlimited
                'max_whatsapp_messages' => 0,
                'max_sms_messages' => 0,
            ],
            [
                'name' => 'Testing',
                'slug' => 'testing',
                'stripe_price_id' => null,
                'price' => 0,
                'description' => 'Full-access testing plan for development and demos. Managed by Super Admin.',
                'features' => ['All Features Unlocked', 'Unlimited Users', 'Unlimited Messaging', 'No Billing Required', 'Development & Demo Use'],
                'max_users' => 0, // 0 = Unlimited
                'max_appointments' => 0,
                'max_whatsapp_messages' => 0,
                'max_sms_messages' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            \App\Models\Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
