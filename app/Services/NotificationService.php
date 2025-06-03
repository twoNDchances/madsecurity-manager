<?php

namespace App\Services;

use Filament\Notifications\Notification;

class NotificationService
{
    public static function notify(string $status, string $title, string|null $body = null)
    {
        $notification = Notification::make()->title($title)->body($body);
        $notification = match ($status)
        {
            'success' => $notification->success(),
            'failure' => $notification->danger(),
            'warning' => $notification->warning(),
            default => $notification->info(),
        };
        return $notification->send();
    }
}
