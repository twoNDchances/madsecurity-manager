<?php

namespace App\Services;

use Filament\Notifications\Notification;

class NotificationService
{
    private static function getBackbone($status, $title, $body = null)
    {
        $notification = Notification::make()->title($title)->body($body);
        $notification = match ($status)
        {
            'success' => $notification->success(),
            'failure' => $notification->danger(),
            'warning' => $notification->warning(),
            default => $notification->info(),
        };
        return $notification;
    }

    public static function notify($status, $title, $body = null)
    {
        return self::getBackbone($status, $title, $body)->send();
    }

    public static function announce($status, $title, $body = null, $immediately = false)
    {
        $user = AuthenticationService::get();
        return self::getBackbone($status, $title, $body)
        ->sendToDatabase($user, $immediately);
    }
}
