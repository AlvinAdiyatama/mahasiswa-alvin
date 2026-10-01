<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate: aman dijalankan berkali-kali (tidak membuat akun ganda)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin', 'role' => 'admin', 'password' => Hash::make('admin123')]
        );

        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            ['name' => 'User', 'role' => 'user', 'password' => Hash::make('user123')]
        );
    }
}
