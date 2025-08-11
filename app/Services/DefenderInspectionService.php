<?php

namespace App\Services;

use App\Models\Defender;

class DefenderInspectionService extends DefenderPreActionService
{
    protected static ?string $actionType = 'inspect';

    protected static ?string $actionName = 'Data Inspection';

    public static function perform(Defender $defender): string
    {
        $response = HttpRequestService::perform(
            $defender->inspect_method,
            "$defender->url$defender->inspect",
            null,
            false,
            $defender->protection ? $defender->username : null,
            $defender->protection ? $defender->password : null,
            $defender->certification ? storage_path("app/$defender->certification") : null,
        );
        $bodyReturned = null;
        if (is_string($response))
        {
            $bodyReturned = '{}';
            $message = "Defender [$defender->id][$defender->name] inspect failed";
            self::detail('warning', $message, $defender, 'warning');
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
            }
        }
        return $bodyReturned;
    }
}
