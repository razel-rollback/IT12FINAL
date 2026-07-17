<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'fname'          => env('ADMIN_FNAME', 'Admin'),
                'lname'          => env('ADMIN_LNAME', 'User'),
                'contact_number' => env('ADMIN_CONTACT', null),
                'role'           => 'admin',
                'email'          => env('ADMIN_EMAIL', 'admin@admin.com'),
                'password'       => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ],
            [
                'fname'          => env('MANAGER_FNAME', 'Manager'),
                'lname'          => env('MANAGER_LNAME', 'User'),
                'contact_number' => env('MANAGER_CONTACT', null),
                'role'           => 'manager',
                'email'          => env('MANAGER_EMAIL', 'manager@manager.com'),
                'password'       => Hash::make(env('MANAGER_PASSWORD', 'password')),
            ],
            [
                'fname'          => env('CASHIER_FNAME', 'Cashier'),
                'lname'          => env('CASHIER_LNAME', 'User'),
                'contact_number' => env('CASHIER_CONTACT', null),
                'role'           => 'cashier',
                'email'          => env('CASHIER_EMAIL', 'cashier@cashier.com'),
                'password'       => Hash::make(env('CASHIER_PASSWORD', 'password')),
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
