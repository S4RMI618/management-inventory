<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'huessabe@demo.com',
        ], [
            'name' => 'Hugo',
            'email' => 'huessabe@demo.com',
            'password' => Hash::make('sarmi618'),
        ]);
    }
}

