<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HttpRequestService
{
    private static $methods = [
        'get',
        'post',
        'put',
        'patch',
        'delete',
    ];

    public static function perform(string $method, string $url, array|null $body = null, bool $notify = true)
    {
        if (!self::methodExists($method))
        {
            self::notify(
                $notify,
                'info',
                'Method not supported',
                Str::upper($method) . ' method does not exist'
            );
            return null;
        }
        $request = Http::agent()->withHeader('Content-Type', 'application/json');
        $response = null;
        try
        {
            $response = match ($method)
            {
                'get' => $request->get($url, $body),
                'post' => $request->post($url, $body),
                'patch' => $request->patch($url, $body),
                'put' => $request->put($url, $body),
                'delete' => $request->delete($url, $body),
            };
            $description = 'Status Code: ' . $response->status() . ' | Body: ' . $response->body();
            if (!$response->successful())
            {
                self::notify($notify, 'failure', 'Failure', $description);
                return $response;
            }
            self::notify($notify, 'success', 'Success', $description);
        }
        catch (Exception $exception)
        {
            self::notify(
                $notify,
                'warning',
                'Warning',
                'Status Code: 500 | Body: ' . $exception->getMessage(),
            );
        }
        return $response;
    }

    private static function methodExists(string $method)
    {
        return in_array($method, self::$methods);
    }

    private static function notify(bool $active, string $status, string $title, string $body)
    {
        if ($active)
        {
            NotificationService::notify($status, $title, $body);
        }
    }
}
