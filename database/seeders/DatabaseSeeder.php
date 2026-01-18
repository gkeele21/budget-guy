<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user only
        User::create([
            'name' => 'Admin',
            'email' => 'admin@budgetguy.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
