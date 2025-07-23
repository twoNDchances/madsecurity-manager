<?php

namespace App\Services;

use App\Models\Defender;

class DefenderPreActionService
{
    protected static array $requestApiForm = [
        'groups' => [],
        'rules' => [],
        'targets' => [],
        'wordlists' => [],
        'words' => [],
    ];

    protected static ?string $actionType;

    protected static ?string $actionName;

    protected static function clean($data, $more = [])
    {
        $unnecessaries = [
            'description',
            'user_id',
            'created_at',
            'updated_at',
        ];
        if (!empty($more))
        {
            $unnecessaries = array_merge($unnecessaries, $more);
        }
        foreach ($unnecessaries as $unnecessary)
        {
            unset($data[$unnecessary]);
        }
        return $data;
    }

    protected static function log($severity, $action, $message, Defender $defender): string
    {
        $output = match ($severity)
        {
            'notice' => DefenderConsoleService::notice($action, $message),
            'warning' => DefenderConsoleService::warning($action, $message),
            'danger' => DefenderConsoleService::danger($action, $message),
            'emergency' => DefenderConsoleService::emergency($action, $message),
        };
        DefenderConsoleService::updateOutput($defender, $output);
        return $output;
    }

    protected static function detail($severity, $message, Defender $defender, $status): void
    {
        $output = self::log($severity, static::$actionType, $message, $defender);
        NotificationService::announce($status, static::$actionName, $output);
    }

    protected static function send(Defender $defender, $method, $url, $forGroupApi = true): array
    {
        $batchMinSize = 10000;
        $batchMaxSize = 100000;
        foreach (static::$requestApiForm as $_ => $items) {
            if (count($items) > $batchMaxSize) {
                $batchMinSize = $batchMaxSize;
                break;
            }
        }
        $batches = array_map(
            fn($items) => array_chunk($items, $batchMinSize),
            static::$requestApiForm,
        );
        $result = [];
        $maxBatchCount = max(array_map('count', $batches));
        $status = [
            'pass' => 0,
            'fall' => 0,
        ];
        $successIds = [];
        for ($i = 0; $i < $maxBatchCount; $i++)
        {
            $apiBatch = $forGroupApi ? [
                'groups' => $batches['groups'][$i] ?? [],
                'rules' => $batches['rules'][$i] ?? [],
                'targets' => $batches['targets'][$i] ?? [],
                'wordlists' => $batches['wordlists'][$i] ?? [],
                'words' => $batches['words'][$i] ?? [],
            ] : [
                'decisions' => $batches['decisions'][$i] ?? [],
                'wordlists' => $batches['wordlists'][$i] ?? [],
                'words' => $batches['words'][$i] ?? [],
            ];
            $response = HttpRequestService::perform(
                $method,
                $url,
                $apiBatch,
                false,
                $defender->protection ? $defender->username : null,
                $defender->protection ? $defender->password : null,
            );
            if (!is_string($response))
            {
                $message = implode(' | ', [
                    "Batch: $i",
                    'Status: ' . $response->status(),
                    'Body: ' . $response->body(),
                ]);
                $result[] = $message;
                if (!$response->successful())
                {
                    self::detail('danger', $message, $defender, 'failure');
                    $status['fall']++;
                }
                else
                {
                    self::detail('notice', $message, $defender, 'success');
                    $successIds = array_merge(
                        $successIds,
                        array_map(
                            fn($item) => $item['id'] ?? $item,
                            $forGroupApi ? $apiBatch['groups'] : $apiBatch['decisions'],
                        ),
                    );
                    $status['pass']++;
                }
            }
            else
            {
                $message = implode(' | ', [
                    "Batch: $i",
                    explode(' | ', $response)[0],
                    explode(' | ', $response)[1],
                ]);
                $result[] = $message;
                self::detail('danger', $message, $defender, 'failure');
                $status['fall']++;
            }
        }
        NotificationService::notify(null, static::$actionName, implode("\n", $result));
        return match ($status['pass'] > 0)
        {
            true => [
                'status' => true,
                'successIds' => $successIds,
            ],
            false => [
                'status' => false,
                'successIds' => $successIds,
            ],
        };
    }
}
