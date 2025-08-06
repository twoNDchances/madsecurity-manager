<?php

namespace App\Filament\Resources\DefenderResource\RelationManagers;

use App\Filament\Resources\GroupResource;
use App\Tables\GroupTable;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class GroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $icon = 'heroicon-o-rectangle-stack';

    private static $tableRelationship = GroupTable::class;

    protected $listeners = ['refreshGroupTable' => '$refresh'];

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            GroupResource::main(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$tableRelationship::executionOrder(),
            self::$tableRelationship::level(),
            self::$tableRelationship::name(),
            self::$tableRelationship::status(),
            self::$tableRelationship::rules(),
            self::$tableRelationship::tags(),
            self::$tableRelationship::owner(),
        ])
        ->filters([
            //
        ])
        ->headerActions([
            self::$tableRelationship::refreshRelationManagerTable(),
        ])
        ->actions([
            self::$tableRelationship::operationActionGroup(true),
        ])
        ->bulkActions([
            self::$tableRelationship::deleteBulkAction(),
        ]);
    }
}
