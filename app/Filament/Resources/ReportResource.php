<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Forms\ReportForm;
use App\Models\Report;
use App\Tables\ReportTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Audit';

    private static $form = ReportForm::class;

    private static $table = ReportTable::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main()->columnSpanFull(),
        ]);
    }

    private static function main()
    {
        return Forms\Components\Tabs::make()
        ->contained(false)
        ->schema([
            Forms\Components\Tabs\Tab::make('Information')
            ->schema([
                Forms\Components\Grid::make(3)
                ->schema([
                    self::general()->columnSpan(1),
                    self::console()->columnSpan(2),
                ])
            ]),
            Forms\Components\Tabs\Tab::make('Investigation')
            ->schema([
                Forms\Components\Grid::make(2)
                ->schema([
                    self::procedure()->columnSpan(1),
                    self::execution()->columnSpan(1),
                ])
            ]),
        ]);
    }

    private static function general()
    {
        return Forms\Components\Section::make('General')
        ->schema([
            self::$form::defenderName(),
            self::$form::defenderUrl(),
            Forms\Components\Fieldset::make('Details')
            ->schema([
                self::$form::time()->columnSpanFull(),
                self::$form::clientIp()->columnSpan(1),
                self::$form::method()->columnSpan(1),
                self::$form::path()->columnSpanFull(),
                self::$form::userAgent()->columnSpanFull(),
            ]),
        ])
        ->columns(1);
    }

    private static function console()
    {
        return Forms\Components\Section::make('Console')
        ->schema([
            self::$form::output(),
        ])
        ->columns(1);
    }

    private static function procedure()
    {
        return Forms\Components\Section::make('Procedure')
        ->schema([
            self::$form::targets()->columns(6),
        ])
        ->columns(1);
    }

    private static function execution()
    {
        return Forms\Components\Section::make('Execution')
        ->schema([
            Forms\Components\Fieldset::make('Rule')
            ->schema([
                self::$form::ruleName()->columnSpan(1),
                self::$form::ruleAlias()->columnSpan(1),
                self::$form::rulePhase()->columnSpanFull(),
                self::$form::ruleTarget()->columnSpan(1),
                self::$form::ruleComparator()->columnSpan(1),
                self::$form::ruleInverse()->columnSpanFull(),
                self::$form::ruleValue()->columnSpanFull(),
                self::$form::ruleWordlist()->columnSpanFull(),
                self::$form::ruleAction()->columnSpan(1),
                self::$form::ruleActionConfiguration()->columnSpan(1),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::time(),
            self::$table::defender(),
            self::$table::clientIp(),
            self::$table::path(),
            self::$table::rule(),
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
            'index' => Pages\ListReports::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 0 ? 'danger' : 'primary';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'time',
            'output',
            'user_agent',
            'client_ip',
            'method',
            'path',
            'getDefender.name',
            'getRule.alias',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->getDefender->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Defender' => $record->getDefender->name,
            'Rule' => $record->getRule->alias,
        ];
    }
}
