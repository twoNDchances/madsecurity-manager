<?php

namespace App\Forms;

use App\Filament\Resources\GroupResource;
use App\Forms\Actions\DefenderAction;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\DefenderValidator;
use Illuminate\Support\Str;

class DefenderForm
{
    private static $validator = DefenderValidator::class;

    private static $action = DefenderAction::class;

    public static function name()
    {
        return FilamentFormService::textInput(
            'name',
            null,
            'Defender Name',
            self::$validator::name(),
        )
        ->required();
    }

    public static function groups($form = true)
    {
        $groupField = FilamentFormService::select(
            'groups',
            null,
            self::$validator::groups(),
        )
        ->relationship('groups', 'name')
        ->searchable()
        ->multiple()
        ->preload();
        if ($form)
        {
            $former = [
                GroupResource::main(false, false, true)->columns(6),
            ];
            $groupField = $groupField
            ->createOptionForm($former);
        }
        return $groupField;
    }

    public static function url()
    {
        return FilamentFormService::textInput(
            'url',
            'URL',
            'Defender URL',
            self::$validator::url(),
        )
        ->required()
        ->unique(ignoreRecord: true)
        ->url()
        ->prefixIcon('heroicon-o-globe-alt')
        ->suffixAction(self::$action::checkHealth());
    }

    public static function path($path)
    {
        return FilamentFormService::textInput(
            $path,
            null,
            'Defender ' . Str::headline($path) . ' Path',
            self::$validator::path(),
        )
        ->required()
        ->default("/$path");
    }

    public static function method($for, $default)
    {
        return FilamentFormService::select(
            $for . '_method',
            Str::title($for) . ' Method',
            self::$validator::method(),
            self::$validator::$methods,
        )
        ->required()
        ->default($default)
        ->selectablePlaceholder(false);
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
            'Some Description for this Defender',
        )
        ->rules(self::$validator::description());
    }

    public static function important()
    {
        return FilamentFormService::toggle(
            'important',
            null,
            self::$validator::important(),
        )
        ->required()
        ->helperText('Ensure only Important Users can operate');
    }

    public static function periodic()
    {
        return FilamentFormService::toggle(
            'periodic',
            null,
            self::$validator::periodic(),
        )
        ->required()
        ->helperText('Automatic periodic Health check');
    }

    public static function protection()
    {
        return FilamentFormService::toggle(
            'protection',
            'Use Basic Authentication',
            self::$validator::protection(),
        )
        ->required()
        ->reactive();
    }

    public static function noCredential()
    {
        $conditon = fn($get) => !$get('protection');
        return FilamentFormService::placeholder(
            'no_credential',
            'Protection will required Credential for Basic Authentication.',
        )
        ->visible($conditon);
    }

    public static function username()
    {
        $conditon = fn($get) => $get('protection');
        return FilamentFormService::textInput(
            'username',
            null,
            'Defender Username',
            self::$validator::username(),
        )
        ->required($conditon)
        ->visible($conditon);
    }

    public static function password()
    {
        $conditon = fn($get) => $get('protection');
        return FilamentFormService::textInput(
            'password',
            null,
            'Defender Password',
            self::$validator::password(),
        )
        ->minLength(8)
        ->required($conditon)
        ->visible($conditon)
        ->password()
        ->revealable();
    }

    public static function totalGroups()
    {
        return FilamentFormService::textInput(
            'total_groups',
            'Total Groups',
            'Total Groups in Defender',
        )
        ->readOnly()
        ->integer();
    }

    public static function currentApplied()
    {
        return FilamentFormService::textInput(
            'current_applied',
            'Current Applied',
            'Current Applied in Defender',
        )
        ->readOnly()
        ->integer();
    }

    public static function output()
    {
        return FilamentFormService::textarea(
            'output',
            null,
        )
        ->readOnly();
    }

    public static function clearOutput()
    {
        return self::$action::clearOutput();
    }

    public static function owner()
    {
        return FilamentFormService::owner();
    }
}
