<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@eduid.com',
            'password' => Hash::make('Admin@123'),
            'role' => 'super_admin',
            'school_id' => null,
            'is_active' => true,
        ]);
    }
}
