<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', env('MANAGER_USER_MAIL', 'root@madsecurity.com'))->first()->id;
        $policies = Permission::getPolicyPermissionOptions();
        $excluded = Permission::flattenExclusionList();
        foreach ($policies as $key => $value)
        {
            if (in_array($key, $excluded))
            {
                continue;
            }
            Permission::createOrFirst(
                [
                    'name' => $value,
                    'action' => $key
                ],
                [
                    'name' => $value,
                    'action' => $key,
                    'user_id' => $user,
                ]
            );
        }
    }
}
