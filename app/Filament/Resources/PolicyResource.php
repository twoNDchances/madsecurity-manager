<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PolicyResource\Pages;
use App\Models\Policy;
use App\Services\FilamentColumnService;
use App\Services\FilamentFormService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class PolicyResource extends Resource
{
    protected static ?string $model = Policy::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';

    protected static ?string $navigationGroup = 'Privileges';

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
            self::information()->columns(1)->columnSpan(2),
            self::scope()->columnSpan(1),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Policy Information')
        ->schema([
            self::setName(),
            self::setPermissions(),
            self::setDescription(),
        ]);
    }
    
    private static function scope()
    {
        return Forms\Components\Section::make('Policy Scope')
        ->schema([
            self::setUsers(),
        ]);
    }

    private static function setName()
    {
        $rules = [
            'required',
            'string',
            'max:255',
        ];
        return FilamentFormService::textInput(
            'name',
            null,
            'Permission Name',
            $rules
        )
        ->required();
    }

    private static function setPermissions()
    {
        $rules = [
            'nullable',
            Rule::exists('permissions', 'id'),
        ];
        $former = [
            PermissionResource::main(),
        ];
        return FilamentFormService::select(
            'permissions',
            'Permissions',
            null,
            $rules,
        )
        ->relationship('permissions', 'name')
        ->multiple()
        ->searchable()
        ->preload()
        ->createOptionForm($former);
    }

    private static function setDescription()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Description for this Policy'
        );
    }

    private static function setUsers()
    {
        $rules = [
            'nullable',
            Rule::exists('users', 'id'),
        ];
        $former = [
            UserResource::main(),
        ];
        return FilamentFormService::select(
            'users',
            'Users',
            null,
            $rules,
        )
        ->relationship('users', 'name')
        ->multiple()
        ->searchable()
        ->preload()
        ->createOptionForm($former);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            FilamentColumnService::text('name'),
            self::getUsers(),
            self::getPermissions(),
            FilamentColumnService::text('getOwner.email', 'Created by'),
        ])
        ->filters([
            //
        ])
        ->actions([
            FilamentColumnService::actionGroup(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    private static function getUsers()
    {
        return FilamentColumnService::text('users.email', 'Users')
        ->listWithLineBreaks()
        ->bulleted()
        ->limitList(5)
        ->expandableLimitedList();
    }

    private static function getPermissions()
    {
        return FilamentColumnService::text('permissions.name', 'Permissions')
        ->listWithLineBreaks()
        ->bulleted()
        ->limitList(5)
        ->expandableLimitedList();
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
}
