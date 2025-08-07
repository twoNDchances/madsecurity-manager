<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Tables\Actions\FingerprintAction;
use Illuminate\Support\Str;

class FingerprintTable
{
    private static $action = FingerprintAction::class;

    public static function createdAt()
    {
        return FilamentTableService::text('created_at', 'Performed at')
        ->dateTime()
        ->timezone(config('app.timezone', 'Asian/Ho_Chi_Minh'));
    }

    public static function owner()
    {
        return FilamentTableService::text('getOwner.email', 'Performed by');
    }

    public static function ipAddress()
    {
        return FilamentTableService::text('ip_address', 'IP Address');
    }

    public static function httpMethod()
    {
        $colors = fn($state) => match ($state)
        {
            'GET' => 'success',
            'POST' => 'pink',
            'PUT' => 'primary',
            'PATCH' => 'info',
            'DELETE' => 'danger',
            default => 'warning',
        };
        return FilamentTableService::text('http_method', 'HTTP Method')
        ->badge()
        ->color($colors);
    }

    public static function route()
    {
        return FilamentTableService::text('route');
    }

    public static function action()
    {
        $colors = fn($state) => match ($state)
        {
            'Create' => 'success',
            'Update' => 'info',
            'Delete' => 'danger',
            'Check Health' => 'slate',
            'Collect All' => 'teal',
            'Apply', 'Apply All' => 'sky',
            'Revoke', 'Revoke All' => 'pink',
            'Implement', 'Implement All' => 'orange',
            'Suspend', 'Suspend All' => 'yellow',
            default => 'warning',
        };
        return FilamentTableService::text('action')
        ->badge()
        ->color($colors);
    }

    public static function resource()
    {
        $state = fn($state) => class_basename($state);
        $url = function($record)
        {
            $resourceName = Str::lower(class_basename($record->resource_type));
            return route('filament.manager.resources.' . Str::plural($resourceName) . '.edit', ['record' => $record->resource_id]);
        };
        return FilamentTableService::text('resource_type', 'Resource')
        ->formatStateUsing($state)
        ->url($url)
        ->openUrlInNewTab();
    }

    public static function actionGroup()
    {
        return self::$action::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return self::$action::deleteBulkAction();
    }
}
