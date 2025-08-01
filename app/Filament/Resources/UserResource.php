<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Forms\UserForm;
use App\Models\User;
use App\Services\AuthenticationService;
use App\Tables\UserTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Privileges';

    private static $form = UserForm::class;

    private static $table = UserTable::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main(),
        ]);
    }

    public static function main($policy = true, $token = true)
    {
        return Forms\Components\Grid::make(3)
        ->schema([
            self::information()->columns(2)->columnSpan(2),
            Forms\Components\Grid::make(1)
            ->schema([
                self::scope($policy),
                self::access($token),
            ])
            ->columnSpan(1),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('User Information')
        ->schema([
            self::$form::name(),
            self::$form::email(),
            self::$form::password()->columnSpanFull(),
            self::$form::tags()->columnSpanFull(),
            self::$form::verification()->columnSpanFull(),
        ]);
    }

    private static function scope($policy = true)
    {
        return Forms\Components\Section::make('User Scope')
        ->schema([
            self::$form::policies($policy),
            self::$form::activation(),
            self::$form::important(),
        ]);
    }

    private static function access($token = true)
    {
        return Forms\Components\Section::make('User Access')
        ->schema([
            self::$form::tokens($token),
        ]);
    }

    public static function table(Table $table): Table
    {
        $query = User::query();
        $user = AuthenticationService::get();
        if (!$user->important)
        {
            $query->where('important',false);
        }
        return $table
        ->query($query)
        ->columns([
            self::$table::name(),
            self::$table::email(),
            self::$table::activation(),
            self::$table::verification(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $user = AuthenticationService::get();
        $model = static::getModel();
        return !$user->important ? $model::where('important', false)->count() : $model::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'email',
            'active',
            'important',
            'getSuperior.name',
            'getSuperior.email',
            'tags.name',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->email;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Name' => $record->name,
            'Active' => $record->active ? 'Yes' : 'No',
        ];
    }
}
