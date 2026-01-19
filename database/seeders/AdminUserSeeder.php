<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Create just the admin user without any other data.
     * Run with: php artisan db:seed --class=AdminUserSeeder
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@budgetguy.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
