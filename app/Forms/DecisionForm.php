<?php

namespace App\Forms;

use App\Filament\Resources\DefenderResource;
use App\Filament\Resources\WordlistResource;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\GUI\DecisionValidator;
use Illuminate\Support\Str;

class DecisionForm
{
    private static $validator = DecisionValidator::class;

    public static function score()
    {
        return FilamentFormService::textInput(
            'score',
            null,
            'Score',
            self::$validator::score(),
        )
        ->required()
        ->integer()
        ->maxValue(999999999)
        ->minValue(-999999999);
    }

    public static function phaseType()
    {
        $colors = [
            'request' => 'info',
            'response' => 'danger',
        ];
        $state = fn($set) => $set('action', null);
        return FilamentFormService::toggleButton(
            'phase_type',
            'Phase Type',
            self::$validator::phaseType(),
            self::$validator::$phaseTypes,
            $colors,
        )
        ->required()
        ->afterStateUpdated($state)
        ->default('request')
        ->reactive();
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
            'Some Description for this Decision',
        )
        ->rules(self::$validator::description());
    }

    public static function name()
    {
        return FilamentFormService::textInput(
            'name',
            null,
            'Decision Name',
            self::$validator::name(),
        )
        ->required()
        ->unique(ignoreRecord: true);
    }

    public static function defenders($form = true)
    {
        $defenderField = FilamentFormService::select(
            'defenders',
            null,
            self::$validator::defenders(),
        )
        ->relationship('defenders', 'url')
        ->multiple()
        ->searchable()
        ->preload();
        if ($form)
        {
            $former = [
                DefenderResource::main(false),
            ];
            $defenderField = $defenderField
            ->createOptionForm($former);
        }
        return $defenderField;
    }

    public static function action()
    {
        $options = fn($get) => self::$validator::$actions[$get('phase_type')];
        $state = fn($set) => $set('action_configuration', null);
        return FilamentFormService::select(
            'action',
            null,
            self::$validator::action(),
            $options,
        )
        ->required()
        ->reactive()
        ->afterStateUpdated($state);
    }

    public static function placeholder()
    {
        $condition = fn($get) => !in_array(
            $get('action'),
            ['redirect', 'tag', 'kill', 'warn'],
        );
        $content = function($get)
        {
            $content = 'No action selected';
            if ($get('action'))
            {
                $content = Str::headline($get('action')) . ' action no needs configuration';
            }
            return $content;
        };
        return FilamentFormService::placeholder('')
        ->visible($condition)
        ->content($content);
    }

    public static function redirect()
    {
        $condition = fn($get) => $get('action') == 'redirect';
        return FilamentFormService::textInput(
            'action_configuration',
            'URL',
            'Redirect URL',
            self::$validator::redirect(),
        )
        ->required($condition)
        ->visible($condition)
        ->url();
    }

    public static function killHeader()
    {
        $condition = fn($get) => $get('action') == 'kill';
        return FilamentFormService::textInput(
            'kill_header',
            'Header',
            'Session ID Header',
            self::$validator::killHeader(),
        )
        ->required($condition)
        ->visible($condition);
    }

    public static function killPath()
    {
        $condition = fn($get) => $get('action') == 'kill';
        return FilamentFormService::textInput(
            'kill_path',
            'Path',
            'Backend Path',
            self::$validator::killPath(),
        )
        ->required($condition)
        ->visible($condition);
    }

    public static function wordlist()
    {
        $condition = fn($get) => in_array(
            $get('action'),
            ['tag', 'warn'],
        );
        $former = [
            WordlistResource::main(),
        ];
        return FilamentFormService::select(
            'getWordlist',
            'Wordlist Alias',
            self::$validator::wordlist(),
        )
        ->relationship('getWordlist', 'alias')
        ->searchable()
        ->preload()
        ->required($condition)
        ->visible($condition)
        ->createOptionForm($former);
    }
}
