<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WordlistResource\Pages;
use App\Models\Wordlist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WordlistResource extends Resource
{
    protected static ?string $model = Wordlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Managements';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            //
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
            Tables\Actions\EditAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
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
            'index' => Pages\ListWordlists::route('/'),
            'create' => Pages\CreateWordlist::route('/create'),
            'edit' => Pages\EditWordlist::route('/{record}/edit'),
        ];
    }
}
