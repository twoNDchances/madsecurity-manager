<?php

namespace App\Forms;

use App\Filament\Resources\PolicyResource;
use App\Models\Permission;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\GUI\PermissionValidator;

class PermissionForm
{
    private static $validator = PermissionValidator::class;

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

    public static function action()
    {
        $options = Permission::getAvailablePermissions();
        return FilamentFormService::select(
            'action',
            null,
            self::$validator::action(),
            $options,
        )
        ->required()
        ->searchable();
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
            'Some Description about this Permission'
        )
        ->rules(self::$validator::description());
    }

    public static function policies($form = true)
    {
        $policyField = FilamentFormService::select(
            'policies',
            'Policies',
            self::$validator::policies(),
        )
        ->relationship('policies', 'name')
        ->multiple()
        ->searchable()
        ->preload();
        if ($form)
        {
            $former = [
                PolicyResource::main(false, false, true, true),
            ];
            $policyField = $policyField
            ->createOptionForm($former);
        }
        return $policyField;
    }

    public static function owner()
    {
        return FilamentFormService::owner();
    }
}
