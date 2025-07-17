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
                    'alias' => 'header-keys-request',
                    'type' => 'header',
                    'name' => 'keys',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'header-values-request',
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
                    'alias' => 'header-size-request',
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
                    'alias' => 'client-protocol',
                    'type' => 'client',
                    'name' => 'protocol',
                    'datatype' => 'string',
                ],
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
                [
                    'alias' => 'full-header-request',
                    'type' => 'full',
                    'name' => 'raw',
                    'datatype' => 'string',
                ],
            ],
            2 => [
                [
                    'alias' => 'body-keys-request',
                    'type' => 'body',
                    'name' => 'keys',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'file-keys-request',
                    'type' => 'file',
                    'name' => 'keys',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'body-values-request',
                    'type' => 'body',
                    'name' => 'values',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'file-values-request',
                    'type' => 'file',
                    'name' => 'values',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'file-names-request',
                    'type' => 'file',
                    'name' => 'names',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'file-extensions-request',
                    'type' => 'file',
                    'name' => 'extensions',
                    'datatype' => 'array',
                ],
                // ========================= //
                [
                    'alias' => 'body-size-request',
                    'type' => 'body',
                    'name' => 'size',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'file-size-request',
                    'type' => 'file',
                    'name' => 'size',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'file-name-size-request',
                    'type' => 'file',
                    'name' => 'name-size',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'body-length-request',
                    'type' => 'body',
                    'name' => 'length',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'file-length-request',
                    'type' => 'file',
                    'name' => 'length',
                    'datatype' => 'number',
                ],
                // ========================= //
                [
                    'alias' => 'full-body-request',
                    'type' => 'full',
                    'name' => 'raw',
                    'datatype' => 'string',
                ],
            ],
            3 => [
                [
                    'alias' => 'header-keys-response',
                    'type' => 'header',
                    'name' => 'keys',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'header-values-response',
                    'type' => 'header',
                    'name' => 'values',
                    'datatype' => 'array',
                ],
                // ========================= //
                [
                    'alias' => 'header-size-response',
                    'type' => 'header',
                    'name' => 'size',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'server-status',
                    'type' => 'server',
                    'name' => 'status',
                    'datatype' => 'number',
                ],
                // ========================= //
                [
                    'alias' => 'server-protocol',
                    'type' => 'server',
                    'name' => 'protocol',
                    'datatype' => 'string',
                ],
                [
                    'alias' => 'full-header-response',
                    'type' => 'full',
                    'name' => 'raw',
                    'datatype' => 'string',
                ],
            ],
            4 => [
                [
                    'alias' => 'body-keys-response',
                    'type' => 'body',
                    'name' => 'keys',
                    'datatype' => 'array',
                ],
                [
                    'alias' => 'body-values-response',
                    'type' => 'body',
                    'name' => 'values',
                    'datatype' => 'array',
                ],
                // ========================= //
                [
                    'alias' => 'body-size-response',
                    'type' => 'body',
                    'name' => 'size',
                    'datatype' => 'number',
                ],
                [
                    'alias' => 'body-length-response',
                    'type' => 'body',
                    'name' => 'length',
                    'datatype' => 'number',
                ],
                // ========================= //
                [
                    'alias' => 'full-body-response',
                    'type' => 'full',
                    'name' => 'raw',
                    'datatype' => 'string',
                ],
            ],
            5 => [
                [
                    'alias' => 'full-response',
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
