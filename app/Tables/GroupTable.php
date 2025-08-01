<?php

namespace App\Tables;

use App\Services\AuthenticationService;
use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use App\Tables\Actions\GroupAction;
use App\Validators\GUI\GroupValidator;

class GroupTable
{
    private static $action = GroupAction::class;

    private static $user = AuthenticationService::class;

    private static $validator = GroupValidator::class;

    public static function executionOrder()
    {
        if (!AuthenticationService::can(self::$user::get(), 'group', 'update'))
        {
            return FilamentTableService::text(
                'execution_order',
                'Execution Order',
            );
        }
        return FilamentTableService::textInput(
            'execution_order',
            'Execution Order',
            self::$validator::executionOrder(),
        );
    }

    public static function level()
    {
        if (!AuthenticationService::can(self::$user::get(), 'group', 'update'))
        {
            return FilamentTableService::text('level');
        }
        return FilamentTableService::textInput(
            'level',
            null,
            self::$validator::level(),
        );
    }

    public static function name()
    {
        return FilamentTableService::text('name');
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

    public static function rules()
    {
        return FilamentTableService::text('rules.alias')
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
