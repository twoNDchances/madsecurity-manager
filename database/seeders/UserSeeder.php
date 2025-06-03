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
        $mail = env('MANAGER_USER_MAIL', 'root@madsecurity.com');
        User::firstOrCreate(
            ['email' => $mail],
            [
                'name' => env('MANAGER_USER_NAME', 'root'),
                'email' => $mail,
                'password' => Hash::make(env('MANAGER_USER_PASS', 'root')),
                'important' => true,
                'active' => true,
                'user_id' => 1,
            ]
        );
    }
}
