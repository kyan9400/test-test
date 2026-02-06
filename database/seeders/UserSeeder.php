<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('admin');

        $manager1 = User::firstOrCreate(
            ['email' => 'manager1@example.com'],
            [
                'name' => 'Manager One',
                'password' => Hash::make('password'),
            ]
        );
        $manager1->assignRole('manager');

        $manager2 = User::firstOrCreate(
            ['email' => 'manager2@example.com'],
            [
                'name' => 'Manager Two',
                'password' => Hash::make('password'),
            ]
        );
        $manager2->assignRole('manager');

        User::factory(3)->create()->each(function (User $user) {
            $user->assignRole('manager');
        });
    }
}
