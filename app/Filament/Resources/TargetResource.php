<?php

namespace App\Filament\Resources;

use App\Tables\TargetTable;
use App\Filament\Resources\TargetResource\Pages;
use App\Forms\TargetForm;
use App\Models\Target;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TargetResource extends Resource
{
    protected static ?string $model = Target::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static ?string $navigationGroup = 'Managements';

    private static $form = TargetForm::class;

    private static $table = TargetTable::class;

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
                self::$form::name()->columnSpanFull(),
                self::$form::alias()->columnSpanFull(),
            ])->columnSpan(1),
            Forms\Components\Fieldset::make('Attribution')
            ->schema([
                self::$form::datatype()->columnSpanFull(),
                self::$form::wordlist()->columnSpanFull(),
            ])->columnSpan(1),
            Forms\Components\Fieldset::make('Transformation')
            ->schema([
                self::$form::engine(),
                self::$form::placeholder(),
                self::$form::indexOf(),
                self::$form::number(),
                self::$form::hash(),
            ])->columnSpanFull(),
            self::$form::tags(),
            self::$form::description(),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Target Information')
        ->schema([
            self::$form::phase(),
            self::$form::type(),
            self::$form::superior(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::name(),
            self::$table::phase(),
            self::$table::alias(),
            self::$table::wordlist(),
            self::$table::datatype(),
            self::$table::engine(),
            self::$table::finalDatatype(),
            self::$table::superior(),
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
            'index' => Pages\ListTargets::route('/'),
            'create' => Pages\CreateTarget::route('/create'),
            'edit' => Pages\EditTarget::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'alias',
            'type',
            'name',
            'phase',
            'datatype',
            'final_datatype',
            'engine',
            'engine_configuration',
            'description',
            'immutable',
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
            'Type' => $record->type,
            'Phase' => $record->phase,
        ];
    }
}
