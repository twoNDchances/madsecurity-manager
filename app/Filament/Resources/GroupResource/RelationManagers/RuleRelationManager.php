<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Filament\Resources\RuleResource;
use App\Tables\RuleTable;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RuleRelationManager extends RelationManager
{
    protected static string $relationship = 'rules';

    private static $tableRelationship = RuleTable::class;

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            RuleResource::main(),
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
            Tables\Actions\CreateAction::make()->icon('heroicon-o-plus'),
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
