<?php

namespace App\Forms;

use App\Forms\Actions\FingerprintAction;
use App\Services\FilamentFormService;
use Illuminate\Support\Str;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class FingerprintForm
{
    private static $action = FingerprintAction::class;

    public static function owner()
    {
        return FilamentFormService::select(
            'getOwner',
            'Performed by',
        )
        ->relationship('getOwner', 'email');
    }

    public static function ipAddress()
    {
        return FilamentFormService::textInput(
            'ip_address',
            'IP Address',
        );
    }

    public static function userAgent()
    {
        return FilamentFormService::textarea(
            'user_agent',
            'User Agent',
        );
    }

    public static function httpMethod()
    {
        $options = [
            'GET' => 'GET',
            'POST' => 'POST',
            'PUT' => 'PUT',
            'PATCH' => 'PATCH',
            'DELETE' => 'DELETE',
        ];
        $colors = [
            'GET' => 'success',
            'POST' => 'pink',
            'PUT' => 'primary',
            'PATCH' => 'info',
            'DELETE' => 'danger',
        ];
        return FilamentFormService::toggleButton(
            'http_method',
            'HTTP Method',
            [],
            $options,
            $colors,
        );
    }

    public static function route()
    {
        return FilamentFormService::textInput(
            'route',
        )
        ->prefixIcon('heroicon-o-globe-alt');
    }

    public static function action()
    {
        return FilamentFormService::textInput(
            'action',
        );
    }

    public static function resource()
    {
        $state = function($record, $set)
        {
            $resourceName = Str::lower(class_basename($record->resource_type));
            $routeName = 'filament.manager.resources.' . Str::plural($resourceName);
            try
            {
                $routeName = route("$routeName.edit", ['record' => $record->resource_id]);
            }
            catch (RouteNotFoundException $exception)
            {
                $routeName = route("$routeName.index");
            }
            $set('resource', $routeName);
        };
        return FilamentFormService::textInput(
            'resource',
        )
        ->prefixIcon('heroicon-o-link')
        ->suffixAction(self::$action::openResource())
        ->afterStateHydrated($state);
    }

    public static function at()
    {
        return FilamentFormService::dateTimePicker(
            'created_at',
            'Performed at',
        );
    }
}
