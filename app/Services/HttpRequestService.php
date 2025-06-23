<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
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

    public static function perform($method, $url, $body = null, $notify = true, $username = null, $password = null): Response|string
    {
        if (!self::methodExists($method))
        {
            $body = Str::upper($method) . ' method does not exist';
            self::notify(
                $notify,
                'info',
                'Method not supported',
                $body,
            );
            return $body;
        }
        $request = Http::agent()->withHeader('Content-Type', 'application/json');
        if ($username && $password) {
            $request = $request->withBasicAuth($username, $password);
        }
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
            return $response;
        }
        catch (Exception $exception)
        {
            $body = 'Status Code: 500 | Body: ' . $exception->getMessage();
            self::notify(
                $notify,
                'warning',
                'Warning',
                $body,
            );
            return $body;
        }
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
