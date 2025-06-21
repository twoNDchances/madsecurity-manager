<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Tables\RuleTable;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class RulesRelationManager extends RelationManager
{
    protected static string $relationship = 'rules';

    private static $tableRelationship = RuleTable::class;

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            //
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$tableRelationship::representation(),
            self::$tableRelationship::phase(),
            self::$tableRelationship::target(),
            self::$tableRelationship::inverse(),
            self::$tableRelationship::comparator(),
            self::$tableRelationship::value(),
            self::$tableRelationship::wordlist(),
            self::$tableRelationship::action(),
            self::$tableRelationship::severity(),
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
            self::$tableRelationship::actionGroup(),
        ])
        ->bulkActions([
            self::$tableRelationship::deleteBulkAction(),
        ])
        ->reorderable('position');
    }
}
