<?php

namespace App\Services;

use App\Models\Defender;

class DefenderInspectService extends DefenderPreActionService
{
    protected static ?string $actionType = 'inspect';

    protected static ?string $actionName = 'Data Inspection';

    public static function perform(Defender $defender, $notify = true): string
    {
        $response = self::request(
            $defender,
            $defender->inspect_method,
            "$defender->url$defender->inspect",
            null,
            false,
        );
        $bodyReturned = null;
        if (is_string($response))
        {
            $bodyReturned = '{}';
            $message = "Defender [$defender->id][$defender->name] inspect failed";
            self::detail('warning', $message, $defender, 'warning');
            if ($notify)
            {
                NotificationService::notify('warning', self::$actionName, $response);
            }
        }
        else
        {
            if ($response->successful())
            {
                $bodyReturned = json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                self::detail('notice', 'omitted', $defender, 'info');
            }
            else
            {
                $bodyReturned = '{}';
                $body = 'Status Code: ' . $response->status() . ' | Body: ' . $response->body();
                self::detail('warning', $body, $defender, 'warning');
                if ($notify)
                {
                    NotificationService::notify('warning', self::$actionName, $body);
                }
            }
        }
        return $bodyReturned;
    }
}
