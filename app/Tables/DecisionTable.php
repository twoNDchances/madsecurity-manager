<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use App\Tables\Actions\DecisionAction;

class DecisionTable
{
    private static $action = DecisionAction::class;

    public static function name()
    {
        return FilamentTableService::text('name');
    }

    public static function score()
    {
        return FilamentTableService::text('score');
    }

    public static function phaseType()
    {
        $colors = fn($state) => match ($state)
        {
            'request' => 'info',
            'response' => 'danger',
        };
        return FilamentTableService::text('phase_type')
        ->badge()
        ->color($colors);
    }

    public static function action()
    {
        return FilamentTableService::text('action');
    }

    public static function status()
    {
        return FilamentTableService::icon('pivot.status', 'Status')
        ->boolean();
    }

    public static function defenders()
    {
        return FilamentTableService::text('defenders.name')
        ->bulleted()
        ->limitList(3)
        ->expandableLimitedList()
        ->listWithLineBreaks();
    }

    public static function tags()
    {
        return TagFieldService::getTags();
    }

    public static function owner()
    {
        return FilamentTableService::text('getOwner.email', 'Created by');
    }

    public static function actionGroup()
    {
        return self::$action::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return self::$action::deleteBulkAction();
    }

    public static function operationActionGroup()
    {
        return self::$action::operationActionGroup();
    }
}
