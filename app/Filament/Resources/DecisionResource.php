<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DecisionResource\Pages;
use App\Forms\DecisionForm;
use App\Models\Decision;
use App\Tables\DecisionTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DecisionResource extends Resource
{
    protected static ?string $model = Decision::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'Managements';

    private static $form = DecisionForm::class;

    private static $table = DecisionTable::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main(),
        ]);
    }

    public static function main($defender = true)
    {
        return Forms\Components\Grid::make(3)
        ->schema([
            self::information()->columnSpan(1),
            self::definition($defender)->columnSpan(2),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Decision Information')
        ->schema([
            self::$form::score(),
            self::$form::phaseType(),
            self::$form::tags(),
        ])
        ->columns(1);
    }

    private static function definition($defender = true)
    {
        return Forms\Components\Section::make('Decision Definition')
        ->schema([
            self::$form::name()->columnSpan(1),
            self::$form::defenders($defender)->columnSpan(1),
            Forms\Components\Fieldset::make('Task')
            ->schema([
                self::$form::action()->columnSpan(1),
                Forms\Components\Fieldset::make('Configuration')
                ->schema([
                    self::$form::placeholder(),
                    self::$form::redirect(),
                    self::$form::killHeader(),
                    self::$form::killPath(),
                    self::$form::wordlist(),
                ])
                ->columns(1)
                ->columnSpan(1),
            ]),
            self::$form::description()->columnSpanFull(),
        ])
        ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::name(),
            self::$table::score(),
            self::$table::phaseType(),
            self::$table::action(),
            self::$table::defenders(),
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
            'index' => Pages\ListDecisions::route('/'),
            'create' => Pages\CreateDecision::route('/create'),
            'edit' => Pages\EditDecision::route('/{record}/edit'),
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
            'phase_type',
            'score',
            'action',
            'action_configuration',
            'getWordlist.name',
            'getOwner.name',
            'tags.name',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Phase Type' => $record->phase_type,
            'Score' => $record->score,
            'Action' => $record->action,
        ];
    }
}
