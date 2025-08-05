<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RuleResource\Pages;
use App\Forms\RuleForm;
use App\Models\Rule;
use App\Tables\RuleTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RuleResource extends Resource
{
    protected static ?string $model = Rule::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Managements';

    private static $form = RuleForm::class;

    private static $table = RuleTable::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main(),
        ]);
    }

    public static function main($group = true)
    {
        return Forms\Components\Grid::make(3)
        ->schema([
            self::core($group)->columns(2)->columnSpan(2),
            self::logistic()->columns(2)->columnSpan(1),
        ]);
    }

    public static function core($group = true)
    {
        return Forms\Components\Wizard::make([
            Forms\Components\Wizard\Step::make('Information')
            ->schema([
                self::$form::phase()->columnSpan(1),
                Forms\Components\Fieldset::make('Representation')
                ->schema([
                    self::$form::name()->columnSpanFull(),
                    self::$form::alias()->columnSpanFull(),
                ])
                ->columnSpan(1),
            ]),

            Forms\Components\Wizard\Step::make('Definition')
            ->schema([
                self::$form::target(),
                self::$form::comparator(),
                self::$form::inverse()->columnSpanFull(),
                self::$form::value()->columnSpanFull(),
                self::$form::anyNumber()->columnSpanFull(),
                self::$form::range()->columnSpanFull(),
                self::$form::wordlist()->columnSpanFull(),
                self::$form::action()->columnSpan(1),
                self::$form::actionConfiguration()->columns(1)->columnSpan(1),
            ]),

            Forms\Components\Wizard\Step::make('Completion')
            ->schema([
                self::$form::groups($group)->columnSpan(1),
                self::$form::tags()->columnSpan(1),
                self::$form::description()->columnSpanFull(),
            ]),
        ]);
    }

    public static function logistic()
    {
        return Forms\Components\Section::make('Logistic')
        ->schema([
            self::$form::log()->columnSpanFull(),
            self::$form::logisticOption('time'),
            self::$form::logisticOption('user_agent', 'User Agent'),
            self::$form::logisticOption('client_ip'),
            self::$form::logisticOption('method'),
            self::$form::logisticOption('path', 'URL Path'),
            self::$form::logisticOption('output'),
            self::$form::logisticOption('target'),
            self::$form::logisticOption('rule'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::representation(),
            self::$table::phase(),
            self::$table::target(),
            self::$table::inverse(),
            self::$table::comparator(),
            self::$table::value(),
            self::$table::wordlist(),
            self::$table::action(),
            self::$table::severity(),
            self::$table::groups(),
            self::$table::tags(),
            self::$table::owner(),
        ])
        ->filters([
            //
        ])
        ->actions([
            self::$table::actionGroup(),
        ])
        ->bulkActions([
            self::$table::deleteBulkAction(),
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
            'index' => Pages\ListRules::route('/'),
            'create' => Pages\CreateRule::route('/create'),
            'edit' => Pages\EditRule::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'alias',
            'phase',
            'getTarget.name',
            'getTarget.alias',
            'comparator',
            'inverse',
            'value',
            'action',
            'action_configuration',
            'log',
            'time',
            'user_agent',
            'client_ip',
            'method',
            'path',
            'severity',
            'description',
            'getOwner.name',
            'getOwner.email',
            'tags.name',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->alias;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name' => $record->name,
            'Phase' => $record->phase,
        ];
    }
}
