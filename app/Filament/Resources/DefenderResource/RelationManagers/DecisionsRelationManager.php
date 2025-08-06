<?php

namespace App\Filament\Resources\DefenderResource\RelationManagers;

use App\Filament\Resources\DecisionResource;
use App\Tables\DecisionTable;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class DecisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'decisions';

    protected static ?string $icon = 'heroicon-o-scale';

    private static $tableRelationship = DecisionTable::class;

    protected $listeners = ['refreshDecisionTable' => '$refresh'];

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            DecisionResource::main()
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$tableRelationship::name(),
            self::$tableRelationship::score(),
            self::$tableRelationship::phaseType(),
            self::$tableRelationship::action(),
            self::$tableRelationship::status(),
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
