<?php

namespace App\Forms;

use App\Filament\Resources\GroupResource;
use App\Filament\Resources\GroupResource\Pages\CreateGroup;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\DefenderValidator;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Str;

class DefenderForm
{
    private static $validator = DefenderValidator::class;

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
        ->prefixIcon('heroicon-o-globe-alt');
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
                GroupResource::main(false)->columns(6),
            ];
            $creator = fn($data) => CreateGroup::callByStatic($data)->id;
            $groupField = $groupField
            ->createOptionForm($former)
            ->createOptionUsing($creator);
        }
        return $groupField;
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

    public static function description()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Some Description for this Defender',
        )
        ->rules(self::$validator::description());
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

    public static function status()
    {
        return FilamentFormService::toggleButton(
            'status',
            null,
            [],
            [
                true => 'Healthy',
                false => 'Unhealthy',
            ],
            [
                true => 'success',
                false => 'danger',
            ],
        )
        ->disabled();
    }

    public static function current()
    {
        return FilamentFormService::textInput(
            'current',
            'Applied Count',
        )
        ->integer()
        ->disabled()
        ->prefixIcon('heroicon-o-rectangle-stack');
    }

    public static function output()
    {
        $state = function ($record, $set)
        {
            if ($record) {
                $set('output', implode("\n", $record->output));
            }
        };
        return FilamentFormService::textarea(
            'output',
            null,
        )
        ->readOnly()
        ->afterStateHydrated($state);
    }

    public static function clearOutput()
    {
        return Action::make('clear_output')
        ->label('Clear Output')
        ->action(null)
        ->icon('heroicon-o-backspace');
    }

    public static function tags()
    {
        return TagFieldService::setTags();
    }
}
