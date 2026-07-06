<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'  => 'Super Admin',
                'email' => 'superadmin@tekiotask.my',
                'role'  => 'SuperAdmin',
            ],
            [
                'name'  => 'Admin User',
                'email' => 'admin@tekiotask.my',
                'role'  => 'Administrator',
            ],
            [
                'name'  => 'Ms. Sarah',
                'email' => 'teacher@tekiotask.my',
                'role'  => 'Teacher',
            ],
            [
                'name'  => 'Dr. Amir',
                'email' => 'therapist@tekiotask.my',
                'role'  => 'Therapist',
            ],
            [
                'name'  => 'Parent User',
                'email' => 'parent@tekiotask.my',
                'role'  => 'Parent',
            ],
            [
                'name'                   => 'Leo',
                'email'                  => 'student@tekiotask.my',
                'role'                   => 'Student',
                'age'                    => 10,
                'diagnosis'              => 'ADHD',
                'accessibility_settings' => json_encode([
                    'large_buttons' => true,
                    'high_contrast' => false,
                    'mute_sounds'   => true,
                    'text_size'     => 18,
                ]),
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password_hash' => Hash::make('password123'),
                ])
            );
        }

        $this->command->info(' All 6 test users seeded. Password: password123');
    }
}
