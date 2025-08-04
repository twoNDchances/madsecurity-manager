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
use Illuminate\Database\Eloquent\Model;

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

    public static function main($policy = true, $owner = false)
    {
        $form = [
            self::information()->columns(2)->columnSpan(2),
            self::scope($policy)->columnSpan(1),
        ];
        if ($owner)
        {
            $form[] = self::$form::owner();
        }
        return Forms\Components\Grid::make(3)
        ->schema($form);
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

    private static function scope($policy = true)
    {
        return Forms\Components\Section::make('Permission Scope')
        ->schema([
            self::$form::policies($policy),
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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'action',
            'description',
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
            'Action' => $record->action,
        ];
    }
}
