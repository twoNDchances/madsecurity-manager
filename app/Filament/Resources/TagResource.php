<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Forms\TagForm;
use App\Models\Tag;
use App\Tables\TagTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $navigationGroup = 'Utilities';

    private static $form = TagForm::class;

    private static $table = TagTable::class;

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
            self::information()->columns(2)->columnSpanFull(),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Tag Information')
        ->schema([
            Forms\Components\Grid::make(1)
            ->schema([
                self::$form::name(),
                self::$form::color(),
            ])
            ->columnSpan(1),
            self::$form::description()->columnSpan(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::name(),
            self::$table::color(),
            self::$table::types('groups.name'),
            self::$table::types('permissions.name'),
            self::$table::types('policies.name'),
            self::$table::types('rules.alias'),
            self::$table::types('targets.alias'),
            self::$table::types('users.email'),
            self::$table::types('wordlists.alias'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
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
            'color',
            'description',
            'getOwner.name',
            'getOwner.email',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Color' => $record->color,
        ];
    }
}
