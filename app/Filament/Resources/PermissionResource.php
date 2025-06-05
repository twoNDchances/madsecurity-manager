<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Models\Permission;
use App\Services\FilamentColumnService;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

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
            self::information()->columns(2)->columnSpan(2),
            self::scope()->columnSpan(1),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Permission Information')
        ->schema([
            self::setName(),
            self::setAction(),
            self::setTags()->columnSpanFull(),
            self::setDescription()->columnSpanFull(),
        ]);
    }

    private static function scope()
    {
        return Forms\Components\Section::make('Permission Scope')
        ->schema([
            self::setPolicies(),
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
        ->required()
        ->unique(ignoreRecord: true);
    }

    private static function setAction()
    {
        $options = Permission::getAvailablePermissions();
        $rules = [
            'required',
            Rule::in(array_keys($options)),
        ];
        return FilamentFormService::select(
            'action',
            null,
            $options,
            $rules,
        )
        ->required()
        ->searchable();
    }

    private static function setTags()
    {
        return TagFieldService::setTags();
    }

    private static function setDescription()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Description for this Permission'
        );
    }

    private static function setPolicies()
    {
        $rules = [
            'nullable',
            Rule::exists('policies', 'id'),
        ];
        return FilamentFormService::select(
            'policies',
            'Policies',
            null,
            $rules,
        )
        ->relationship('policies', 'name')
        ->multiple()
        ->searchable()
        ->preload();
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::getName(),
            self::getResource(),
            self::getAction(),
            self::getPolicies(),
            self::getTags(),
            self::getOwner(),
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

    private static function getName()
    {
        return FilamentColumnService::text('name');
    }

    private static function getResource()
    {
        $state = fn($record) => Str::title(explode('.', $record->action)[0]);
        return FilamentColumnService::text('resource')->getStateUsing($state);
    }

    private static function getAction()
    {
        $state = fn($record) => Str::headline(explode('.', $record->action)[1]);
        return FilamentColumnService::text('action')->getStateUsing($state);
    }

    private static function getPolicies()
    {
        return FilamentColumnService::text('policies.name', 'Policies')
        ->listWithLineBreaks()
        ->bulleted()
        ->limitList(5)
        ->expandableLimitedList();
    }

    private static function getTags()
    {
        return TagFieldService::getTags();
    }

    private static function getOwner()
    {
        return FilamentColumnService::text('getOwner.email', 'Created by');
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
