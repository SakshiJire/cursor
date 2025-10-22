<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@chatapi.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Staff Users
        User::create([
            'name' => 'John Staff',
            'email' => 'staff1@chatapi.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Staff',
            'email' => 'staff2@chatapi.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'email_verified_at' => now(),
        ]);

        // Create Student Users
        User::create([
            'name' => 'Alice Student',
            'email' => 'student1@chatapi.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Bob Student',
            'email' => 'student2@chatapi.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Charlie Student',
            'email' => 'student3@chatapi.com',
            'password' => Hash::make('password'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // Create Parent Users
        User::create([
            'name' => 'David Parent',
            'email' => 'parent1@chatapi.com',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Eve Parent',
            'email' => 'parent2@chatapi.com',
            'password' => Hash::make('password'),
            'role' => 'parent',
            'email_verified_at' => now(),
        ]);
    }
}
