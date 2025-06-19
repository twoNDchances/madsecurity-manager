<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PolicyResource\Pages;
use App\Forms\PolicyForm;
use App\Models\Policy;
use App\Tables\PolicyTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PolicyResource extends Resource
{
    protected static ?string $model = Policy::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $navigationGroup = 'Privileges';

    private static $form = PolicyForm::class;

    private static $table = PolicyTable::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main(),
        ]);
    }

    public static function main($permission = true, $user = true)
    {
        return Forms\Components\Grid::make(3)
        ->schema([
            self::information($permission)->columns(2)->columnSpan(2),
            self::scope($user)->columnSpan(1),
        ]);
    }

    private static function information($permission = true)
    {
        return Forms\Components\Section::make('Policy Information')
        ->schema([
            self::$form::name(),
            self::$form::permissions($permission),
            self::$form::tags()->columnSpanFull(),
            self::$form::description()->columnSpanFull(),
        ]);
    }
    
    private static function scope($user = true)
    {
        return Forms\Components\Section::make('Policy Scope')
        ->schema([
            self::$form::users($user),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::name(),
            self::$table::users(),
            self::$table::permissions(),
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
            'index' => Pages\ListPolicies::route('/'),
            'create' => Pages\CreatePolicy::route('/create'),
            'edit' => Pages\EditPolicy::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
