<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Forms\PermissionForm;
use App\Models\Permission;
use App\Tables\PermissionTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Privileges';

    private static $form = PermissionForm::class;

    private static $table = PermissionTable::class;

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
            self::information()->columns(2)->columnSpan(2),
            self::scope()->columnSpan(1),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Permission Information')
        ->schema([
            self::$form::name(),
            self::$form::action(),
            self::$form::tags()->columnSpanFull(),
            self::$form::description()->columnSpanFull(),
        ]);
    }

    private static function scope()
    {
        return Forms\Components\Section::make('Permission Scope')
        ->schema([
            self::$form::policies(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::name(),
            self::$table::resource(),
            self::$table::action(),
            self::$table::policies(),
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
