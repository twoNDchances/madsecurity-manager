<?php

namespace Database\Seeders;

use App\Models\Tag;
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
        $user = User::firstOrCreate(
            ['email' => $mail],
            [
                'name' => env('MANAGER_USER_NAME', 'root'),
                'email' => $mail,
                'password' => Hash::make(env('MANAGER_USER_PASS', 'root')),
                'email_verified_at' => now(),
                'important' => true,
                'active' => true,
                'user_id' => 1,
            ]
        );
        $tag = Tag::where('name', 'default assets')->first();
        $tag->users()->sync($user->id);
        $tag->update(['user_id' => $user->id]);
    }
}
