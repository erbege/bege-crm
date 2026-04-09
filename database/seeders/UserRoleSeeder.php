<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'role' => 'super-admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Finance Manager',
                'email' => 'finance@example.com',
                'role' => 'finance',
            ],
            [
                'name' => 'Billing Staff',
                'email' => 'billing@example.com',
                'role' => 'billing',
            ],
            [
                'name' => 'Technical Support',
                'email' => 'tech@example.com',
                'role' => 'technical-support',
            ],
            [
                'name' => 'Customer Support',
                'email' => 'support@example.com',
                'role' => 'customer-support',
            ],
            [
                'name' => 'Operator',
                'email' => 'operator@example.com',
                'role' => 'operator',
            ],
            [
                'name' => 'Marketing',
                'email' => 'marketing@example.com',
                'role' => 'marketing',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Assign role if not already assigned
            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
