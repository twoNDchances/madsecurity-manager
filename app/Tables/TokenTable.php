<?php

namespace App\Tables;

use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use App\Tables\Actions\TokenAction;
use Carbon\Carbon;

class TokenTable
{
    private static $action = TokenAction::class;

    public static function name()
    {
        return FilamentTableService::text('name');
    }

    public static function expiredAt()
    {
        $colors = function($record)
        {
            $time = Carbon::parse($record->expired_at);
            return match(true)
            {
                $time->isPast() => 'danger',
                $time->diffInHours(now()) <= 24 => 'primary',
                default => 'success',
            }; 
        };
        return FilamentTableService::text('expired_at', 'Expired At')
        ->dateTime()
        ->badge()
        ->color($colors)
        ->timezone('Asia/Ho_Chi_Minh');
    }

    public static function users()
    {
        return FilamentTableService::text('users.email', 'Users')
        ->listWithLineBreaks()
        ->bulleted()
        ->limitList(5)
        ->expandableLimitedList();
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
}
