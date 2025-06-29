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

    private static $tableRelationship = GroupTable::class;

    protected $listeners = ['refreshGroupTable' => '$refresh'];

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            GroupResource::main()->columns(6),
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
            //
        ])
        ->actions([
            self::$tableRelationship::operationActionGroup(),
        ])
        ->bulkActions([
            self::$tableRelationship::deleteBulkAction(),
        ]);
    }
}
