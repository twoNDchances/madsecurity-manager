<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers\RulesRelationManager;
use App\Forms\GroupForm;
use App\Models\Group;
use App\Tables\GroupTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Managements';

    private static $form = GroupForm::class;

    private static $table = GroupTable::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main(),
        ]);
    }

    public static function main($rule = true, $defender = true)
    {
        return Forms\Components\Grid::make(3)
        ->schema([
            self::information()->columnSpan(1),
            self::definition($rule, $defender)->columnSpan(2),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Group Information')
        ->schema([
            self::$form::executionOrder(),
            self::$form::level(),
        ])
        ->columns(1);
    }

    private static function definition($rule = true, $defender = true)
    {
        return Forms\Components\Section::make('Group Definition')
        ->schema([
            self::$form::name()->columnSpan(1),
            self::$form::defenders($defender)->columnSpan(1),
            self::$form::rules($rule)->columnSpanFull(),
            self::$form::tags()->columnSpan(1),
            self::$form::description()->columnSpan(1),
        ])
        ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::executionOrder(),
            self::$table::level(),
            self::$table::name(),
            self::$table::defenders(),
            self::$table::rules(),
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
            RulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
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
            'execution_order',
            'level',
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
            'Execution Order' => $record->execution_order,
            'Level' => $record->level,
        ];
    }
}
