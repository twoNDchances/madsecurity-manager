<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WordlistResource\Pages;
use App\Forms\WordlistForm;
use App\Models\Wordlist;
use App\Tables\WordlistTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class WordlistResource extends Resource
{
    protected static ?string $model = Wordlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-down';

    protected static ?string $navigationGroup = 'Managements';

    private static $form = WordlistForm::class;

    private static $table = WordlistTable::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main(),
        ]);
    }

    public static function main()
    {
        return Forms\Components\Grid::make(2)
        ->schema([
            self::information()->columns(2)->columnSpan(1),
            self::definition()->columnSpan(1),
        ]);
    }

    public static function information()
    {
        return Forms\Components\Section::make('Wordlist Information')
        ->schema([
            self::$form::name(),
            self::$form::alias(),
            self::$form::tags()->columnSpanFull(),
            self::$form::description()->columnSpanFull(),
        ]);
    }

    public static function definition()
    {
        return Forms\Components\Section::make('Wordlist Definition')
        ->schema([
            self::$form::content(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::name(),
            self::$table::alias(),
            self::$table::counter(),
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
            'index' => Pages\ListWordlists::route('/'),
            'create' => Pages\CreateWordlist::route('/create'),
            'edit' => Pages\EditWordlist::route('/{record}/edit'),
        ];
    }
}
