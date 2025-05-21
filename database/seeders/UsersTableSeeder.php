<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Админ',
            'email' => 'admin@example.com',
            'phone' => '1234567890',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
    }
}