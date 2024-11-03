<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Doe',                       // Add a name for the user
                'email' => 'Jihadae54@gmail.com',           // First email
                'password' => bcrypt('password123'),        // Use a secure password
                'domin_name' => 'jihadadomain.com',         // Sample domain name
                'reset_code' => null,                        // Initially null
                'reset_code_expiry' => null,                 // Initially null
                'is_admin' => true,                          // Set to true if admin
            ],
            [
                'name' => 'Jane Smith',                      // Add a name for the user
                'email' => 'Jeolord37@gmail.com',            // Second email
                'password' => bcrypt('password123'),        // Use a secure password
                'domin_name' => 'jeolorddomain.com',        // Sample domain name
                'reset_code' => null,                        // Initially null
                'reset_code_expiry' => null,                 // Initially null
                'is_admin' => false,                         // Set to false if not admin
            ],
            [
                'name' => 'Alice Johnson',                   // Add a name for the user
                'email' => 'morshedy480@gmail.com',          // Third email
                'password' => bcrypt('password123'),        // Use a secure password
                'domin_name' => 'morshedydomain.com',       // Sample domain name
                'reset_code' => null,                        // Initially null
                'reset_code_expiry' => null,                 // Initially null
                'is_admin' => false,                         // Set to false if not admin
            ],
        ];

        // Insert each user into the database
        foreach ($users as $user) {
            User::create($user);
        }

    }
}
