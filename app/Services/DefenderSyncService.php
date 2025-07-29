<?php

namespace App\Services;

use App\Models\Defender;
use Illuminate\Support\Str;

class DefenderSyncService extends DefenderPreActionService
{
    protected static ?string $actionType = 'sync';

    protected static ?string $actionName = 'Data Synchronization';

    public static function perform(Defender $defender): Defender
    {
        $response = self::fetch($defender);
        if ($response["status"] != 200)
        {
            self::detail('danger', $response['message'], $defender, 'failure');
        }
        else
        {
            dd($response['data']);
            // self::detail('notice', $response['message'], $defender, 'success');
            // NotificationService::notify(null, static::$actionName, json_encode($response['data']['counters']));
        }
        return $defender;
    }

    private static function fetch(Defender $defender): array
    {
        $request = HttpRequestService::perform(
            'post',
            "$defender->url$defender->sync?page=1&pageSize=1",
            null,
            false,
            $defender->protection ? $defender->username : null,
            $defender->protection ? $defender->password : null,
        );
        if (is_string($request))
        {
            $response = explode(' | ', $request);
            return [
                'status' => 500,
                'data' => null,
                'message' => count($response) == 2 ? Str::replaceFirst('Body: ', '', $response[1]) : 'Unknown',
            ];
        }
        $response = $request->json('data');
        $data = [
            'counters' => $response['counters'],
            'decisions' => $response['resources']['decisions'],
            'groups' => $response['resources']['groups'],
            'rules' => $response['resources']['rules'],
            'targets' => $response['resources']['targets'],
            'wordlists' => $response['resources']['wordlists'],
            'words' => $response['resources']['words'],
        ];
        // Danh sách các resource có thể có
        $resources = ['decisions', 'groups', 'rules', 'targets', 'wordlists', 'words'];

        foreach ($resources as $resource) {
            $remainingPages = $response['pages']['remainingPages'][$resource] ?? 0;

            // Nếu còn trang cần lấy
            for ($page = 2; $page <= $remainingPages + 1; $page++) {
                $url = "$defender->url$defender->sync?page=$page&pageSize=1";
                $nextRequest = HttpRequestService::perform(
                    'post',
                    $url,
                    null,
                    false,
                    $defender->protection ? $defender->username : null,
                    $defender->protection ? $defender->password : null,
                );

                if (!is_string($nextRequest)) {
                    $more = $nextRequest->json('data.resources.' . $resource) ?? [];

                    // Nếu là mảng, merge vào dữ liệu chính
                    if (is_array($more)) {
                        $data[$resource] = array_merge($data[$resource], $more);
                    }
                } else {
                    // Gặp lỗi thì bỏ qua trang này hoặc ghi log tuỳ bạn
                    continue;
                }
            }
        }

        return [
            'status' => $request->status(),
            'data' => $data,
            'message' => $request->json('message'),
        ];
    }
}
