<?php

namespace App\Forms;

use App\Filament\Resources\PermissionResource;
use App\Filament\Resources\PermissionResource\Pages\CreatePermission;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\PolicyValidator;

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

    public static function permissions()
    {
        $former = [
            PermissionResource::main(),
        ];
        $creator = fn($data) => CreatePermission::callByStatic($data);
        return FilamentFormService::select(
            'permissions',
            'Permissions',
            self::$validator::permissions(),
        )
        ->relationship('permissions', 'name')
        ->multiple()
        ->searchable()
        ->preload()
        ->createOptionForm($former)
        ->createOptionUsing($creator);
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
            'Description for this Policy'
        )
        ->rules(self::$validator::description());
    }

    public static function users()
    {
        $former = [
            UserResource::main(),
        ];
        $creator = fn($data) => CreateUser::callByStatic($data);
        return FilamentFormService::select(
            'users',
            'Users',
            self::$validator::users(),
        )
        ->relationship('users', 'name')
        ->multiple()
        ->searchable()
        ->preload()
        ->createOptionForm($former)
        ->createOptionUsing($creator);
    }
}
