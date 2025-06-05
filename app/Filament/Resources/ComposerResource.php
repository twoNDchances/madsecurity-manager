<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComposerResource\Pages;
use App\Models\Composer;
use App\Services\FilamentColumnService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComposerResource extends Resource
{
    protected static ?string $model = Composer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $navigationGroup = 'Extensions';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main(),
        ]);
    }

    public static function main()
    {
        return Forms\Components\Grid::make(2)
        ->schema([
            self::information()->columnSpan(1),
            self::description()->columnSpan(1),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Composer Information')
        ->schema([

        ]);
    }

    private static function description()
    {
        return Forms\Components\Section::make('Composer Description')
        ->schema([

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComposers::route('/'),
            'create' => Pages\CreateComposer::route('/create'),
            'edit' => Pages\EditComposer::route('/{record}/edit'),
        ];
    }
}
