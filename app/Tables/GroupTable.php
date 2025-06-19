<?php

namespace App\Tables;

use App\Services\AuthenticationService;
use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use App\Validators\GroupValidator;
use Filament\Tables\Actions\DeleteBulkAction;

class GroupTable
{
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
        return FilamentTableService::icon('status')
        ->boolean();
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
        return FilamentTableService::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return DeleteBulkAction::make();
    }
}
