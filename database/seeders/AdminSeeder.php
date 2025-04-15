<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah admin sudah ada
        $existingAdmin = User::where('username', 'admin@attendx.com')->first();
        
        if (!$existingAdmin) {
            User::create([
                'username' => 'admin@attendx.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'is_active' => true,
            ]);

            $this->command->info('Admin user created successfully.');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
}