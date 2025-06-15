<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TargetResource\Pages;
use App\Filament\Resources\WordlistResource\Pages\CreateWordlist;
use App\Models\Target;
use App\Models\Wordlist;
use App\Services\FilamentColumnService;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TargetResource extends Resource
{
    protected static ?string $model = Target::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $navigationGroup = 'Managements';

    private static array $phases = [
        1 => '1. Header Request',
        2 => '2. Body Request',
        3 => '3. Header Response',
        4 => '4. Body Response',
    ];

    private static array $types = [
        1 => [
            'target' => 'Target',
            'header' => 'Header',
            'url.args' => 'URL Arguments',
        ],
        2 => [],
        3 => [],
        4 => [],
    ];

    private static array $datatypes = [
        'array' => 'Array',
        'number' => 'Number',
        'string' => 'String',
    ];

    private static array $engines = [
        'array' => [
            'indexOf' => 'Index Of',
        ],
        'number' => [
            'addition' => 'Addition',
            'subtraction' => 'Subtraction',
            'multiplication' => 'Multiplication',
            'division' => 'Division',
            'powerOf' => 'Power Of',
            'remainder' => 'Remainder',
        ],
        'string' => [
            'lower' => 'Lower',
            'upper' => 'Upper',
            'capitalize' => 'Capitalize',
            'trim' => 'Trim',
            'trimLeft' => 'Trim Left',
            'trimRight' => 'Trim Right',
            'removeWhitespace' => 'Remove Whitespace',
            'length' => 'Length',
            'hash' => 'Hash',
        ],
    ];

    private static array $hashes = [
        'md5' => 'MD5',
        'sha1' => 'SHA128',
        'sha256' => 'SHA256',
        'sha512' => 'SHA512',
    ];

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main(),
        ]);
    }

    public static function main()
    {
        return Forms\Components\Grid::make(3)
        ->schema([
            self::definition()->columns(2)->columnSpan(2),
            self::information()->columnSpan(1),
        ]);
    }

    private static function definition()
    {
        return Forms\Components\Section::make('Target Definition')
        ->schema([
            Forms\Components\Fieldset::make('Representation')
            ->schema([
                self::setName()->columnSpanFull(),
                self::setAlias()->columnSpanFull(),
            ])->columnSpan(1),
            Forms\Components\Fieldset::make('Attribution')
            ->schema([
                self::setDatatype()->columnSpanFull(),
                self::setWordlist()->columnSpanFull(),
            ])->columnSpan(1),
            Forms\Components\Fieldset::make('Transformation')
            ->schema([
                self::setEngine(),
                self::setPlaceholder(),
                self::setIndexOf(),
                self::setNumber(),
                self::setHash(),
            ])->columnSpanFull(),
            self::setTags(),
            self::setDescription(),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Target Information')
        ->schema([
            self::setPhase(),
            self::setType(),
            self::setSuperior(),
        ]);
    }

    private static function setName()
    {
        $rules = [
            'required',
            'string',
            'max:255',
        ];
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
            return 'The ' . Str::title($get('datatype')) . ' datatype requires a single value';
        };
        return FilamentFormService::textInput(
            'name',
            null,
            'Target Name',
            $rules,
        )
        ->required()
        ->readOnly($condition)
        ->afterStateUpdated($state)
        ->helperText($helperText);
    }

    private static function setAlias()
    {
        $rules = [
            'required',
            'string',
            'max:255',
            'alpha_dash',
            function($record)
            {
                if ($record)
                {
                    return Rule::unique('targets', 'alias')->ignore($record->id);
                }
                return 'unique:targets,alias';
            },
        ];
        return FilamentFormService::textInput(
            'alias',
            null,
            'Target Alias',
            $rules,
        )
        ->required()
        ->alphaDash()
        ->unique(ignoreRecord: true)
        ->helperText('Autofill if left blank');
    }

    private static function setDatatype()
    {
        $rules = [
            'required',
            'string',
            Rule::in(array_keys(self::$datatypes)),
        ];
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
                return;
            }
        };
        $condition = fn($get) => $get('target_id') ? true : false;
        return FilamentFormService::toggleButton(
            'datatype',
            null,
            $rules,
            self::$datatypes,
        )
        ->required()
        ->inline()
        ->default('array')
        ->colors($colors)
        ->reactive()
        ->afterStateUpdated($state)
        ->disabled($condition)
        ->dehydrated();
    }

    private static function setWordlist()
    {
        $rules = [
            'nullable',
            'integer',
            Rule::exists('wordlists', 'id'),
        ];
        $condition = fn($get) => $get('datatype') == 'array' && !$get('target_id');
        $former = [
            WordlistResource::main(),
        ];
        $creator = fn($data) => CreateWordlist::callByStatic($data)->id;
        return FilamentFormService::select(
            'wordlist_id',
            null,
            [],
            $rules,
        )
        ->visible($condition)
        ->relationship('getWordlist', 'alias')
        ->searchable()
        ->preload()
        ->createOptionForm($former)
        ->createOptionUsing($creator)
        ->helperText('The Array datatype can use a Wordlist Alias, line break for each element');
    }

    private static function setEngine()
    {
        $rules = [
            'nullable',
            'string',
            fn($get) => $get('datatype') ? Rule::in(array_keys(self::$engines[$get('datatype')])) : null,
        ];
        $options = fn($get) => $get('datatype') ? self::$engines[$get('datatype')] : [];
        return FilamentFormService::select(
            'engine',
            null,
            $options,
            $rules,
        )
        ->searchable()
        ->reactive();
    }

    private static function setPlaceholder()
    {
        $condition = fn($get) => !in_array($get('engine'), [
            'indexOf', 'addition', 'subtraction', 'multiplication', 'division', 'powerOf', 'remainder', 'hash'
        ]);
        return FilamentFormService::placeholder(
            'no_configuration', 
            'Engine currently does not need configuration'
        )
        ->visible($condition);
    }

    private static function setIndexOf()
    {
        $rules = [
            'required_if:engine,indexOf',
            'integer',
            'min:0',
            fn($get) => function($attribute, $value, $fail) use ($get)
            {
                $wordlist = null;
                if ($get('target_id'))
                {
                    $target = Target::find($get('target_id'));
                    if ($target->immutable)
                    {
                        return;
                    }
                    $root = Target::getRoot($target);
                    $wordlist = Wordlist::find($root->wordlist_id);
                }

                if ($get('wordlist_id'))
                {
                    $wordlist = Wordlist::find($get('wordlist_id'));
                }

                $counter = $wordlist->words()->count();
                if ($counter == 0 || ($counter - 1) < $value)
                {
                    $fail("The {$attribute} has crossed the limit, total {$counter}");
                    return;
                }
                $fail("The Wordlist required for {$attribute}");
            }
        ];
        $condition = fn($get) => $get('engine') == 'indexOf';
        return FilamentFormService::textInput(
            'engine_configuration',
            'Index',
            'Index Of Array',
            $rules,
        )
        ->required($condition)
        ->visible($condition)
        ->integer()
        ->minValue(0);
    }

    private static function setNumber()
    {
        $rules = [
            'required_if:engine,addition,subtraction,multiplication,division,powerOf,remainder',
            'numeric'
        ];
        $condition = fn($get) => in_array($get('engine'), array_keys(self::$engines['number']));
        return FilamentFormService::textInput(
            'engine_configuration',
            'Number',
            'Number',
            $rules,
        )
        ->required($condition)
        ->visible($condition)
        ->integer();
    }

    private static function setHash()
    {
        $rules = [
            'required_if:engine,hash',
            Rule::in(array_keys(self::$hashes))
        ];
        $condition = fn($get) => $get('engine') == 'hash';
        return FilamentFormService::select(
            'engine_configuration',
            'Algorithm',
            self::$hashes,
            $rules,
        )
        ->required($condition)
        ->visible($condition);
    }

    private static function setTags()
    {
        return TagFieldService::setTags();
    }

    private static function setDescription()
    {
        $rules = [
            'nullable',
            'string',
        ];
        return FilamentFormService::textarea(
            'description',
            null,
            'Some description for this Target',
        )
        ->rules($rules);
    }

    private static function setPhase()
    {
        $rules = [
            'required',
            'integer',
            Rule::in(array_keys(self::$phases)),
        ];
        $colors = [
            1 => 'indigo',
            2 => 'primary',
            3 => 'rose',
            4 => 'danger',
        ];
        return FilamentFormService::toggleButton(
            'phase',
            null,
            $rules,
            self::$phases,
        )
        ->required()
        ->reactive()
        ->inline()
        ->colors($colors)
        ->default(1);
    }

    private static function setType()
    {
        $rules = [
            'required',
            'string',
            fn($get) => Rule::in(array_keys(self::$types[(int) $get('phase')]))
        ];
        $options = fn($get) => match ((int) $get('phase'))
        {
            1 => self::$types[1],
            2 => self::$types[2],
            3 => self::$types[3],
            4 => self::$types[4],
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
            $rules,
            $options,
            $colors,
        )
        ->required()
        ->inline()
        ->reactive()
        ->afterStateUpdated($state);
    }

    private static function setSuperior()
    {
        $rules = [
            'required_if:type,target',
            'integer',
            fn($record, $get) => function($attribute, $value, $fail) use ($record, $get)
            {
                $target = Target::find($value);
                if (!$target)
                {
                    $fail("The {$attribute} is invalid");
                    return;
                }
                if ($target->phase != $get('phase'))
                {
                    $fail("The phase of {$attribute} mismatch");
                    return;
                }
                if ($record)
                {
                    if ($record->id == $value)
                    {
                        $fail("The {$attribute} can't reference to itself");
                        return;
                    }

                    if (Target::getRoot($record)->id == $record->id)
                    {
                        $fail("The {$attribute} cannot be selected because it creates a circular reference to itself (via root)");
                        return;
                    }
                }
            },
        ];
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
            }
        };
        $filter = fn($query, $get) => $query->where('phase', $get('phase'));
        return FilamentFormService::select(
            'target_id',
            'Referer',
            [],
            $rules,
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

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            //
        ])
        ->filters([
            //
        ])
        ->actions([
            FilamentColumnService::actionGroup(),
        ])
        ->bulkActions([
            FilamentColumnService::deleteTargetBulkAction(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTargets::route('/'),
            'create' => Pages\CreateTarget::route('/create'),
            'edit' => Pages\EditTarget::route('/{record}/edit'),
        ];
    }
}
