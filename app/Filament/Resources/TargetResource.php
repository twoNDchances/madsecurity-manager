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
            self::information()->columnSpan(1),
            self::definition()->columns(6)->columnSpan(2),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Target Information')
        ->schema([
            self::setPhase(),
            self::setType(),
            self::setSuperior(),
            self::setTags(),
            self::setDescription(),
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
            ])->columnSpan(3),
            Forms\Components\Fieldset::make('Attribution')
            ->schema([
                self::setDatatype()->columnSpanFull(),
                self::setWordlists()->columnSpanFull(),
            ])->columnSpan(3),
            Forms\Components\Fieldset::make('Transformation')
            ->schema([
                self::setEngine(),
                self::setPlaceholder(),
            ]),
        ]);
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
            function($record)
            {
                dd($record);
                if (Target::getRoot($record)->id == $record->id)
                {

                }
            },
        ];
        $condition = fn($get) => $get('type') == 'target';
        return FilamentFormService::select(
            'target_id',
            'Referer',
            [],
            $rules,
        )
        ->required($condition)
        ->visible($condition)
        ->relationship('getSuperior', 'alias')
        ->searchable()
        ->preload();
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

    private static function setName()
    {
        $rules = [
            'required',
            'string',
            'max:255',
        ];
        $condition = fn($get) => $get('target_id') ? true : false;
        return FilamentFormService::textInput(
            'name',
            null,
            'Target Name',
            $rules,
        )
        ->required()
        ->readOnly($condition);
    }

    private static function setAlias()
    {
        $rules = [
            'required',
            'string',
            'max:255',
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
        ->unique(ignoreRecord: true);
    }

    private static function setDatatype()
    {
        $colors = [
            'array' => 'warning',
            'number' => 'success',
            'string' => 'info',
        ];
        $rules = [
            'required',
            'string',
            Rule::in(array_keys(self::$datatypes)),
        ];
        $state = function()
        {

        };
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
        ->afterStateUpdated($state);
    }

    private static function setWordlists()
    {
        $rules = [
            'required_if:datatype,array',
            'integer',
            Rule::exists('wordlists', 'id'),
        ];
        $condition = fn($get) => $get('datatype') == 'array';
        $former = [
            WordlistResource::main(),
        ];
        $creator = fn($data) => CreateWordlist::callByStatic($data)->id;
        return FilamentFormService::select(
            'wordlists',
            null,
            [],
            $rules,
        )
        ->required($condition)
        ->visible($condition)
        ->relationship('wordlists', 'alias')
        ->searchable()
        ->preload()
        ->multiple()
        ->createOptionForm($former)
        ->createOptionUsing($creator);
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
            
        ];
        $condition = fn($get) => $get('engine') == 'indexOf';
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
