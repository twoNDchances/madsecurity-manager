<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::$skipObserver = true;
        Permission::$skipObserver = true;
        Tag::$skipObserver = true;

        $user = User::where('email', env('MANAGER_USER_MAIL', 'root@madsecurity.com'))->first()->id;
        $policies = Permission::getPolicyPermissionOptions();
        $excluded = Permission::flattenExclusionList();
        $ids = [];
        foreach ($policies as $key => $value)
        {
            if (in_array($key, $excluded))
            {
                continue;
            }
            $permsision = Permission::createOrFirst(
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
            $ids[] = $permsision->id;
        }
        Tag::where('name', 'default assets')->first()->permissions()->sync($ids);

        User::$skipObserver = false;
        Permission::$skipObserver = false;
        Tag::$skipObserver = false;
    }
}
