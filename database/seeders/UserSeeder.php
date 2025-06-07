<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

            User::create([
                'name' => 'SuperAdmin',
                'email' => 'SuperAdmin@gmail.com',
                'password' => Hash::make('User3478'),
            ]);

    }
}

