<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TargetResource\Pages;
use App\Models\Target;
use App\Services\FilamentColumnService;
use App\Services\FilamentFormService;
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
        0 => '0. Full Request',
        1 => '1. Header Request',
        2 => '2. Body Request',
        3 => '3. Header Response',
        4 => '4. Body Response',
        5 => '5. Full Response',
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
            self::definition()->columnSpan(2),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Target Definition')
        ->schema([
            self::setPhase(),
            self::setType(),
        ]);
    }

    private static function definition()
    {
        return Forms\Components\Section::make('Target Information')
        ->schema([
            
        ]);
    }

    private static function setPhase()
    {
        $rules = [
            'required',
            'integer',
            Rule::in(array_keys(self::$phases)),
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
        return FilamentFormService::toggleButton(
            'type',
            null,
            $rules,
            $options,
        )
        ->required()
        ->inline()
        ->reactive();
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
