<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ],[
                'name' => 'Personnel',
                'email' => 'staff@example.com',
                'password' => bcrypt('password'),
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate($user);
        }
    }
}
