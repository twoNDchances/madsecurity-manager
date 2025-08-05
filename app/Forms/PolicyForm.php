<?php

namespace App\Forms;

use App\Filament\Resources\PermissionResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\GUI\PolicyValidator;

class PolicyForm
{
    private static $validator = PolicyValidator::class;

    public static function name()
    {
        return FilamentFormService::textInput(
            'name',
            null,
            'Permission Name',
            self::$validator::name(),
        )
        ->required()
        ->unique(ignoreRecord: true);
    }

    public static function permissions($form = true)
    {
        $permissionField = FilamentFormService::select(
            'permissions',
            'Permissions',
            self::$validator::permissions(),
        )
        ->relationship('permissions', 'name')
        ->multiple()
        ->searchable()
        ->preload();
        if ($form)
        {
            $former = [
                PermissionResource::main(false, true),
            ];
            $permissionField = $permissionField
            ->createOptionForm($former);
        }
        return $permissionField;
    }

    public static function tags()
    {
        return TagFieldService::setTags();
    }

    public static function description()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Some Description about this Policy'
        )
        ->rules(self::$validator::description());
    }

    public static function users($form = true)
    {
        $userField = FilamentFormService::select(
            'users',
            'Users',
            self::$validator::users(),
        )
        ->relationship('users', 'name')
        ->multiple()
        ->searchable()
        ->preload();
        if ($form)
        {
            $former = [
                UserResource::main(false, false, true),
            ];
            $creator = fn($data) => CreateUser::callByStatic($data)->id;
            $userField = $userField
            ->createOptionForm($former)
            ->createOptionUsing($creator);
        }
        return $userField;
    }

    public static function owner()
    {
        return FilamentFormService::owner();
    }
}
