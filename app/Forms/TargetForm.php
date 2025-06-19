<?php

namespace App\Forms;

use App\Filament\Resources\WordlistResource;
use App\Filament\Resources\WordlistResource\Pages\CreateWordlist;
use App\Models\Target;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\TargetValidator;
use Illuminate\Support\Str;

class TargetForm
{
    private static $validator = TargetValidator::class;

    public static function name()
    {
        $condition = fn($get) => $get('target_id') ? true : false;
        $state = function($state, $get, $set)
        {
            if (!$get('alias'))
            {
                $set('alias', Str::slug($state));
            }
        };
        $helperText = function($get)
        {
            if ($get('datatype') == 'array')
            {
                return 'Just a name for this Target';
            }
            $datatype = Str::title($get('datatype'));
            return "The $datatype datatype requires a single value";
        };
        return FilamentFormService::textInput(
            'name',
            null,
            'Target Name',
            self::$validator::name(),
        )
        ->required()
        ->readOnly($condition)
        ->afterStateUpdated($state)
        ->helperText($helperText);
    }

    public static function alias()
    {
        return FilamentFormService::textInput(
            'alias',
            null,
            'Target Alias',
            self::$validator::alias(),
        )
        ->required()
        ->alphaDash()
        ->unique(ignoreRecord: true);
    }

    public static function datatype()
    {
        $colors = [
            'array' => 'warning',
            'number' => 'success',
            'string' => 'info',
        ];
        $state = function($state, $set)
        {
            if ($state)
            {
                $set('engine', null);
                $set('number', null);
                $set('indexOf', null);
                $set('hash', null);
            }
        };
        $condition = fn($get) => $get('target_id') ? true : false;
        return FilamentFormService::toggleButton(
            'datatype',
            null,
            self::$validator::datatype(),
            self::$validator::$datatypes,
        )
        ->required()
        ->default('array')
        ->colors($colors)
        ->reactive()
        ->afterStateUpdated($state)
        ->disabled($condition)
        ->dehydrated();
    }

    public static function wordlist()
    {
        $condition = fn($get) => $get('datatype') == 'array' && !$get('target_id');
        $former = [
            WordlistResource::main(),
        ];
        $creator = fn($data) => CreateWordlist::callByStatic($data)->id;
        return FilamentFormService::select(
            'wordlist_id',
            'Wordlist',
            self::$validator::wordlist(),
        )
        ->required($condition)
        ->visible($condition)
        ->relationship('getWordlist', 'alias')
        ->searchable()
        ->preload()
        ->createOptionForm($former)
        ->createOptionUsing($creator)
        ->helperText('The Array datatype can use a Wordlist');
    }

    public static function engine()
    {
        $options = function($get)
        {
            $engines = [];
            if ($get('datatype'))
            {
                $engines = self::$validator::$engines[$get('datatype')];
            }
            return $engines;
        };
        return FilamentFormService::select(
            'engine',
            null,
            self::$validator::engine(),
            $options,
        )
        ->searchable()
        ->reactive();
    }

    public static function placeholder()
    {
        $condition = function($get)
        {
            $noConfigurations = [
                'indexOf',
                'addition',
                'subtraction',
                'multiplication',
                'division',
                'powerOf',
                'remainder',
                'hash',
            ];
            return !in_array($get('engine'), $noConfigurations);
        };
        return FilamentFormService::placeholder(
            'no_configuration', 
            'Engine currently does not need configuration'
        )
        ->visible($condition);
    }

    public static function indexOf()
    {
        $condition = fn($get) => $get('engine') == 'indexOf';
        return FilamentFormService::textInput(
            'engine_configuration',
            'Index',
            'Index Of Array',
            self::$validator::indexOf(),
        )
        ->required($condition)
        ->visible($condition)
        ->integer()
        ->minValue(0);
    }

    public static function number()
    {
        $condition = fn($get) => in_array(
            $get('engine'),
            array_keys(self::$validator::$engines['number'])
        );
        return FilamentFormService::textInput(
            'engine_configuration',
            'Number',
            'Number',
            self::$validator::number(),
        )
        ->required($condition)
        ->visible($condition)
        ->integer();
    }

    public static function hash()
    {
        $condition = fn($get) => $get('engine') == 'hash';
        return FilamentFormService::select(
            'engine_configuration',
            'Algorithm',
            self::$validator::hash(),
            self::$validator::$hashes,
        )
        ->required($condition)
        ->visible($condition);
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
            'Some Description about this Target',
        )
        ->rules(self::$validator::description());
    }

    public static function phase()
    {
        $colors = [
            1 => 'indigo',
            2 => 'primary',
            3 => 'rose',
            4 => 'danger',
        ];
        return FilamentFormService::toggleButton(
            'phase',
            null,
            self::$validator::phase(),
            self::$validator::$phases,
            $colors,
        )
        ->required()
        ->reactive()
        ->default(1);
    }

    public static function type()
    {
        $options = fn($get) => match ((int) $get('phase'))
        {
            1 => self::$validator::$types[1],
            2 => self::$validator::$types[2],
            3 => self::$validator::$types[3],
            4 => self::$validator::$types[4],
            default => [],
        };
        $colors = [
            'target' => 'purple',
            'header' => 'info',
            'url.args' => 'warning',
        ];
        $state = function ($state, $set)
        {
            if ($state != 'target')
            {
                $set('target_id', null);
                return;
            }
        };
        return FilamentFormService::toggleButton(
            'type',
            null,
            self::$validator::type(),
            $options,
            $colors,
        )
        ->required()
        ->reactive()
        ->afterStateUpdated($state);
    }

    public static function superior()
    {
        $condition = fn($get) => $get('type') == 'target';
        $state = function($state, $set)
        {
            if ($state)
            {
                $target = Target::find($state);
                $set('datatype', $target->final_datatype);
                $set('name', $target->type . '_' . $target->name . '_' . now()->timestamp);
            }
            else
            {
                $set('name', null);
                $set('engine', null);
            }
        };
        $filter = fn($query, $get) => $query->where('phase', $get('phase'));
        return FilamentFormService::select(
            'target_id',
            'Referer',
            self::$validator::superior(),
        )
        ->required($condition)
        ->visible($condition)
        ->relationship(
            'getSuperior',
            'alias',
            $filter,
            true,
        )
        ->searchable()
        ->preload()
        ->reactive()
        ->afterStateUpdated($state);
    }
}
