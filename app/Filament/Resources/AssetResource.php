<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Forms\AssetForm;
use App\Models\Asset;
use App\Tables\AssetTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static ?string $navigationGroup = 'Utilities';

    private static $form = AssetForm::class;

    private static $table = AssetTable::class;

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
            self::definition()->columnSpan(1),
            self::information()->columnSpan(2),
        ]);
    }

    private static function definition()
    {
        return Forms\Components\Section::make('Asset Definition')
        ->schema([
            self::$form::name(),
            self::$form::path(),
            self::$form::tags(),
        ])
        ->columns(1);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Asset Information')
        ->schema([
            Forms\Components\Fieldset::make('Counter')
            ->schema([
                self::$form::totalAsset(),
                self::$form::totalResource(),
                self::$form::failResource(),
            ])
            ->columns(3),
            self::$form::output(),
        ])
        ->columns(1);
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
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
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
            'total_asset',
            'total_resource',
            'fail_resource',
            'getOwner.name',
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
            'Name' => $record->name,
            'Total Asset' => $record->total_asset,
            'Total Resource' => $record->total_resource,
            'Fail Resource' => $record->fail_resource,
        ];
    }
}
