<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'code' => 'BASIC',
                'description' => 'For small schools up to 200 students',
                'price_monthly' => 999.00,
                'price_yearly' => 9999.00,
                'max_students' => 200,
                'max_users' => 10,
                'storage_gb' => 5,
                'custom_templates' => false,
                'analytics_access' => false,
                'api_access' => false,
            ],
            [
                'name' => 'Pro',
                'code' => 'PRO',
                'description' => 'For medium schools up to 1000 students',
                'price_monthly' => 2499.00,
                'price_yearly' => 24999.00,
                'max_students' => 1000,
                'max_users' => 50,
                'storage_gb' => 25,
                'custom_templates' => true,
                'analytics_access' => true,
                'api_access' => false,
            ],
            [
                'name' => 'Enterprise',
                'code' => 'ENTERPRISE',
                'description' => 'Unlimited students with all features',
                'price_monthly' => 4999.00,
                'price_yearly' => 49999.00,
                'max_students' => 99999,
                'max_users' => 500,
                'storage_gb' => 100,
                'custom_templates' => true,
                'analytics_access' => true,
                'api_access' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
