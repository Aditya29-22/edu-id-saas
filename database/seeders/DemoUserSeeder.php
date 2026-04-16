<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create a Demo School
        $school = School::firstOrCreate(
            ['code' => 'DEMO001'],
            [
                'name' => 'Demo International School',
                'email' => 'school@eduid.com',
                'phone' => '1234567890',
                'city' => 'New York',
                'state' => 'NY',
                'is_active' => true,
                'subscription_status' => 'active'
            ]
        );

        // Create School Admin
        User::updateOrCreate(
            ['email' => 'school@eduid.com'],
            [
                'name' => 'Demo School Admin',
                'password' => Hash::make('School@123'),
                'role' => 'school_admin',
                'school_id' => $school->id,
                'is_active' => true,
            ]
        );

        // Create Student account (User)
        User::updateOrCreate(
            ['email' => 'student@eduid.com'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('Student@123'),
                'role' => 'student',
                'school_id' => $school->id,
                'is_active' => true,
            ]
        );

        // Also create a Student record for this user
        Student::firstOrCreate(
            ['roll_number' => 'ROLL-001', 'school_id' => $school->id],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'class_name' => '10',
                'section' => 'A',
                'is_active' => true,
            ]
        );
    }
}
