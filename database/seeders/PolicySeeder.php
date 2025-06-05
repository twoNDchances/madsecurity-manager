<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Policy;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $full = 'Full Access';
        if (!Policy::where('name', $full)->exists())
        {
            $user = User::where('email', env('MANAGER_USER_MAIL', 'root@madsecurity.com'))->first();
            $policy = Policy::create([
                'name' => $full,
                'user_id' => $user->id,
            ]);
            $permissions = Permission::where('action', 'like', '%.all')->pluck('id');
            $policy->permissions()->sync($permissions);
            Tag::where('name', 'default assets')->first()->policies()->sync($policy->id);
        }
    }
}
