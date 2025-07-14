<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Target;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targets = [
            0 => [
                [
                    'alias' => 'full-request',
                    'type' => 'full',
                    'name' => 'raw',
                    'datatype' => 'string',
                ],
            ],

            1 => [
                [
                    'alias' => 'header-keys',
                    'type' => 'header',
                    'name' => 'keys',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'header-values',
                    'type' => 'header',
                    'name' => 'values',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'url-args-keys',
                    'type' => 'url.args',
                    'name' => 'keys',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'url-args-values',
                    'type' => 'url.args',
                    'name' => 'values',
                    'datatype' => 'array',
                ],
                // ========================= //
                [
                    'alias' => 'header-size',
                    'type' => 'header',
                    'name' => 'size',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'url-port',
                    'type' => 'url',
                    'name' => 'port',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'url-args-size',
                    'type' => 'url.args',
                    'name' => 'size',
                    'datatype' => 'number',
                ],
                // ========================= //
                [
                    'alias' => 'client-ip',
                    'type' => 'client',
                    'name' => 'ip',
                    'datatype' => 'string',
                ],
                [
                    'alias' => 'client-method',
                    'type' => 'client',
                    'name' => 'method',
                    'datatype' => 'string',
                ],
                [
                    'alias' => 'url-path',
                    'type' => 'url',
                    'name' => 'path',
                    'datatype' => 'string',
                ],
                [
                    'alias' => 'url-scheme',
                    'type' => 'url',
                    'name' => 'scheme',
                    'datatype' => 'string',
                ],
                [
                    'alias' => 'url-host',
                    'type' => 'url',
                    'name' => 'host',
                    'datatype' => 'string',
                ],
            ],
            2 => [
                [
                    'alias' => 'body-keys',
                    'type' => 'body',
                    'name' => 'keys',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'file-keys',
                    'type' => 'file',
                    'name' => 'keys',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'body-values',
                    'type' => 'body',
                    'name' => 'values',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'file-values',
                    'type' => 'file',
                    'name' => 'values',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'file-names',
                    'type' => 'file',
                    'name' => 'names',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'file-extensions',
                    'type' => 'file',
                    'name' => 'extensions',
                    'datatype' => 'array',
                ],
                // ========================= //
                [
                    'alias' => 'body-size',
                    'type' => 'body',
                    'name' => 'size',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'file-size',
                    'type' => 'file',
                    'name' => 'size',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'file-name-size',
                    'type' => 'file',
                    'name' => 'name-size',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'body-length',
                    'type' => 'body',
                    'name' => 'length',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'file-length',
                    'type' => 'file',
                    'name' => 'length',
                    'datatype' => 'number',
                ],
                // ========================= //
                [
                    'alias' => 'full-body',
                    'type' => 'full',
                    'name' => 'raw',
                    'datatype' => 'string',
                ],
            ],
        ];
        $user = User::where('email', env('MANAGER_USER_MAIL', 'root@madsecurity.com'))->first();
        $ids = [];
        foreach ($targets as $phase => $raw)
        {
            foreach ($raw as $value)
            {
                $target = Target::createOrFirst(
                    [
                        'alias' => $value['alias'],
                        'type' => $value["type"],
                        'name' => $value['name'],
                    ],
                    [
                        'phase' => $phase,
                        'datatype' => $value['datatype'],
                        'final_datatype' => $value['datatype'],
                        'immutable' => true,
                        'user_id' => $user?->id,
                    ]
                );
                $ids[] = $target->id;
            }
        }
        Tag::where('name', 'default assets')->first()->targets()->sync($ids);
    }
}
