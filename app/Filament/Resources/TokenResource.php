<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TokenResource\Pages;
use App\Forms\TokenForm;
use App\Models\Token;
use App\Tables\TokenTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TokenResource extends Resource
{
    protected static ?string $model = Token::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Privileges';

    private static $form = TokenForm::class;

    private static $table = TokenTable::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main(),
        ]);
    }

    public static function main($user = true)
    {
        return Forms\Components\Grid::make(3)
        ->schema([
            self::information()->columnSpan(2),
            self::scope($user)->columnSpan(1),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Token Information')
        ->schema([
            self::$form::name(),
            self::$form::expiredAt(),
            self::$form::value()->columnSpanFull(),
            self::$form::tags()->columnSpanFull(),
            self::$form::description()->columnSpanFull(),
        ])
        ->columns(2);
    }

    private static function scope($user = true)
    {
        return Forms\Components\Section::make('Token Scope')
        ->schema([
            self::$form::users($user),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::name(),
            self::$table::expiredAt(),
            self::$table::users(),
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
            'index' => Pages\ListTokens::route('/'),
            'create' => Pages\CreateToken::route('/create'),
            'edit' => Pages\EditToken::route('/{record}/edit'),
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
            'value',
            'description',
            'expired_at',
            'getOwner.name',
            'getOwner.email',
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
            'Name' => $record->name,
            'Expired at' => $record->expired_at,
        ];
    }
}
