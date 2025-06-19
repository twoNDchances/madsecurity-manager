<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers\RuleRelationManager;
use App\Forms\GroupForm;
use App\Models\Group;
use App\Tables\GroupTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
            self::main()->columns(6),
        ]);
    }

    public static function main($rule = true)
    {
        return Forms\Components\Section::make('Group Information')
        ->aside()
        ->description('Interact with Defender to Apply and Revoke Rules, matching AND logic by grouping multiple Rules together')
        ->schema([
            self::$form::name()->columnSpan(2),
            self::$form::executionOrder()->columnSpan(2),
            self::$form::level()->columnSpan(2),
            self::$form::rules($rule)->columnSpanFull(),
            self::$form::tags()->columnSpan(3),
            self::$form::description()->columnSpan(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::executionOrder(),
            self::$table::level(),
            self::$table::name(),
            self::$table::status(),
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
            RuleRelationManager::class,
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
}
