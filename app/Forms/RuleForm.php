<?php

namespace App\Forms;

use App\Filament\Resources\GroupResource;
use App\Filament\Resources\TargetResource;
use App\Filament\Resources\TargetResource\Pages\CreateTarget;
use App\Filament\Resources\WordlistResource;
use App\Filament\Resources\WordlistResource\Pages\CreateWordlist;
use App\Forms\Actions\RuleAction;
use App\Models\Target;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\RuleValidator;
use Filament\Forms\Components\Fieldset;
use Illuminate\Support\Str;

class RuleForm
{
    private static $validator = RuleValidator::class;

    private static $action = RuleAction::class;

    public static function name()
    {
        $state = function($state, $get, $set)
        {
            if (!$get('alias'))
            {
                $set('alias', Str::slug($state));
            }
        };
        return FilamentFormService::textInput(
            'name',
            null,
            'Rule Name',
            self::$validator::name(),
        )
        ->required()
        ->afterStateUpdated($state);
    }

    public static function alias()
    {
        return FilamentFormService::textInput(
            'alias',
            null,
            'Rule Alias',
            self::$validator::alias(),
        )
        ->required()
        ->unique(ignoreRecord: true)
        ->alphaDash();
    }

    public static function phase()
    {
        $colors = [
            0 => 'sky',
            1 => 'indigo',
            2 => 'primary',
            3 => 'rose',
            4 => 'danger',
            5 => 'pink',
        ];
        $state = fn($set) => $set('target_id', null);
        return FilamentFormService::toggleButton(
            'phase',
            null,
            self::$validator::phase(),
            self::$validator::$phases,
            $colors,
        )
        ->required()
        ->default(0)
        ->reactive()
        ->afterStateUpdated($state);
    }

    public static function target()
    {
        $filter = fn($query, $get) => $query->where('phase', $get('phase'));
        $prefix = fn($state) => Target::find($state)->final_datatype ?? null;
        $former = [
            TargetResource::main(),
        ];
        $creator = fn($data) => CreateTarget::callByStatic($data)->id;
        $state = function($state, $set)
        {
            if (!$state)
            {
                $set('comparator', null);
            }
        };
        return FilamentFormService::select(
            'target_id',
            'Target',
            self::$validator::target(),
        )
        ->relationship(
            'getTarget',
            'alias',
            $filter,
        )
        ->required()
        ->searchable()
        ->preload()
        ->reactive()
        ->prefix($prefix)
        ->createOptionForm($former)
        ->createOptionUsing($creator)
        ->afterStateUpdated($state);
    }

    public static function comparator()
    {
        $options = function($get)
        {
            $finalDatatype = Target::find($get('target_id'))?->final_datatype;
            $comparators = [];
            if ($finalDatatype)
            {
                $comparators = self::$validator::$comparators[$finalDatatype];
            }
            return $comparators;
        };
        $helperText = fn($state) => match ($state)
        {
            '@similar' => 'Target: [Array] @ Value: [WordlistAlias]',
            '@mirror',
            '@regex',
            '@startsWith',
            '@endsWith' => 'Target: [String] @ Value: [String]',
            '@contains' => 'Target: [Array] @ Value: [String]',
            '@in' => 'Target: [String] @ Value: [Array]',
            '@equal',
            '@lessThan',
            '@greaterThan',
            '@lessThanOrEqual',
            '@greaterThanOrEqual' => 'Target: [Number] @ Value: [Number]',
            '@inRange' => 'Target: [NumberFrom] @ Value: [NumberTo]',
            '@check',
            '@checkRegex' => 'Target: [String] @ Value: [WordlistAlias]',
            default => null,
        };
        return FilamentFormService::select(
            'comparator',
            null,
            self::$validator::comparator(),
            $options,
        )
        ->required()
        ->helperText($helperText)
        ->reactive();
    }

    public static function inverse()
    {
        $label = function($get)
        {
            $title = 'Inverse Comparator';
            $comparator = $get('comparator');
            if ($comparator) {
                $comparator = Str::headline(
                    str_replace(
                        '@',
                        '',
                        $comparator
                    ),
                );
                if ($comparator == 'Check Regex') {
                    return "$title: Check Not Regex";
                }
                return "$title: Not $comparator";
            }
            return $title;
        };
        return FilamentFormService::toggle(
            'inverse',
            $label,
            self::$validator::inverse(),
        )
        ->required()
        ->default(false);
    }

    public static function value()
    {
        $condition = fn($get) => !in_array(
            $get('comparator'),
            array_merge(
                [
                    '@similar',
                    '@check',
                    '@checkRegex',
                    '@inRange',
                ],
                array_keys(
                    self::$validator::$comparators['number']
                ),
            )
        );
        return FilamentFormService::textarea(
            'value',
            null,
            'Value for this Rule',
        )
        ->required($condition)
        ->visible($condition)
        ->rules(self::$validator::value());
    }

    public static function anyNumber()
    {
        $condition = function($get) {
            $numbers = self::$validator::$comparators['number'];
            unset($numbers['@inRange']);
            return in_array($get('comparator'), array_keys($numbers));
        };
        return FilamentFormService::textInput(
            'value',
            null,
            'A Number to trigger this Rule',
            self::$validator::anyNumber(),
        )
        ->required($condition)
        ->visible($condition)
        ->numeric();
    }

    public static function range()
    {
        $condition = fn($get) => $get('comparator') == '@inRange';
        return Fieldset::make('Range')
        ->schema([
            self::specificNumber('from'),
            self::specificNumber('to'),
        ])
        ->visible($condition);
    }
    
    private static function specificNumber(string $name)
    {
        $condition = fn($get) => $get('comparator') == '@inRange';
        return FilamentFormService::textInput(
            $name,
            null,
            "Number $name",
            self::$validator::specificNumber(),
        )
        ->required($condition)
        ->visible($condition)
        ->numeric();
    }

    public static function wordlist()
    {
        $condition = fn($get) => in_array(
            $get('comparator'),
            [
                '@similar',
                '@check',
                '@checkRegex',
            ],
        );
        $former = [
            WordlistResource::main(),
        ];
        $creator = fn($data) => CreateWordlist::callByStatic($data)->id;
        return FilamentFormService::select(
            'getWordlist',
            'Wordlist Alias',
            self::$validator::wordlist(),
        )
        ->required($condition)
        ->visible($condition)
        ->relationship('getWordlist', 'alias')
        ->preload()
        ->searchable()
        ->createOptionForm($former)
        ->createOptionUsing($creator);
    }

    public static function action()
    {
        $helperText = fn($state) => match ($state)
        {
            'allow' => 'Stop investigation and accept approval',
            'deny' => 'Stop investigation and refuse to pass',
            'inspect' => 'Increase Score and continue investigation',
            'request' => 'Make an HTTP request and continue investigation',
            'setScore' => 'Reset total Score',
            'setLevel' => 'Reset default Rule enforcement Level',
            'report' => 'Send violation details to Manager and continue investigation',
            'setVariable' => 'Assigns a value to a variable and can be called back by Target until the end of the request\'s lifecycle',
            default => 'No action',
        };
        return FilamentFormService::select(
            'action',
            null,
            self::$validator::action(),
            self::$validator::$actions,
        )
        ->reactive()
        ->helperText($helperText);
    }

    public static function actionConfiguration()
    {
        return Fieldset::make('Configuration')
        ->schema([
            self::placeholder(),
            self::severity(),
            self::requestMethod(),
            self::requestURL(),
            self::score(),
            self::level(),
            self::keyVariable(),
            self::valueVariable(),
        ]);
    }

    private static function placeholder()
    {
        $condition = fn($get) => !in_array(
            $get('action'),
            [
                'inspect',
                'request',
                'setScore',
                'setLevel',
                'setVariable',
            ],
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

    private static function severity()
    {
        $condition = fn($get) => $get('action') == 'inspect';
        $colors = [
            'notice' => 'info',
            'warning' => 'warning',
            'error' => 'danger',
            'critical' => 'purple',
        ];
        return FilamentFormService::toggleButton(
            'severity',
            null,
            self::$validator::severity(),
            self::$validator::$severities,
            $colors,
        )
        ->required($condition)
        ->visible($condition)
        ->inline();
    }

    private static function requestMethod()
    {
        $condition = fn($get) => $get('action') == 'request';
        return FilamentFormService::select(
            'request_method',
            'Request Method',
            self::$validator::requestMethod(),
            self::$validator::$requestMethods,
        )
        ->required($condition)
        ->visible($condition)
        ->default('get')
        ->selectablePlaceholder(false);
    }

    private static function requestURL()
    {
        $condition = fn($get) => $get('action') == 'request';
        return FilamentFormService::textInput(
            'request_url',
            'Request URL',
            'https://example.com',
            self::$validator::requestURL(),
        )
        ->required($condition)
        ->visible($condition)
        ->url()
        ->suffixAction(self::$action::checkConnection());
    }

    private static function score()
    {
        $condition = fn($get) => $get('action') == 'setScore';
        return FilamentFormService::textInput(
            'action_configuration',
            'Set Score',
            'Rule Score',
            self::$validator::score(),
        )
        ->required($condition)
        ->visible($condition)
        ->integer();
    }

    private static function level()
    {
        $condition = fn($get) => $get('action') == 'setLevel';
        return FilamentFormService::textInput(
            'action_configuration',
            'Set Level',
            'Rule Level',
            self::$validator::level(),
        )
        ->required($condition)
        ->visible($condition)
        ->integer()
        ->minValue(1);
    }

    private static function keyVariable()
    {
        $condition = fn($get) => $get('action') == 'setVariable';
        return FilamentFormService::textInput(
            'key_variable',
            'Set Key',
            'Variable Key',
            self::$validator::keyVariable(),
        )
        ->required($condition)
        ->visible($condition)
        ->alphaDash();
    }

    private static function valueVariable()
    {
        $condition = fn($get) => $get('action') == 'setVariable';
        return FilamentFormService::textInput(
            'value_variable',
            'Set Value',
            'Variable Value',
            self::$validator::valueVariable(),
        )
        ->required($condition)
        ->visible($condition);
    }

    public static function groups($form = true)
    {
        $groupField = FilamentFormService::select(
            'groups',
            null,
            self::$validator::groups(),
        )
        ->relationship('groups', 'name', )
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

    public static function tags()
    {
        return TagFieldService::setTags();
    }

    public static function description()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Some Description about this Rule'
        )
        ->rules(self::$validator::description());
    }

    public static function log()
    {
        return FilamentFormService::toggle(
            'log',
            null,
            self::$validator::logistic(),
        )
        ->helperText('When enabled, a Log will be written if a rule is violated')
        ->default(true)
        ->reactive();
    }

    public static function logisticOption($name, $label = null)
    {
        $condition = fn($get) => !$get('log');
        return FilamentFormService::toggle(
            $name,
            $label,
            self::$validator::logistic(),
        )
        ->required()
        ->default(true)
        ->disabled($condition);
    }

    public static function owner()
    {
        return FilamentFormService::owner();
    }
}
