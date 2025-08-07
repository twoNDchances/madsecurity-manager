<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FingerprintResource\Pages;
use App\Forms\FingerprintForm;
use App\Models\Fingerprint;
use App\Tables\FingerprintTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FingerprintResource extends Resource
{
    protected static ?string $model = Fingerprint::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationGroup = 'Audits';

    private static $form = FingerprintForm::class;

    private static $table = FingerprintTable::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main(),
        ]);
    }

    private static function main()
    {
        return Forms\Components\Grid::make(2)
        ->schema([
            self::identification()->columns(2)->columnSpan(1),
            self::information()->columns(3)->columnSpan(1),
        ]);
    }

    private static function identification()
    {
        return Forms\Components\Section::make('Fingerprint Identification')
        ->schema([
            self::$form::owner()->columnSpanFull(),
            self::$form::at(),
            self::$form::ipAddress(),
            self::$form::userAgent()->columnSpanFull(),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Fingerprint Information')
        ->schema([
            self::$form::httpMethod()->columnSpan(2),
            self::$form::action()->columnSpan(1),
            self::$form::route()->columnSpanFull(),
            self::$form::resource()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::createdAt(),
            self::$table::owner(),
            self::$table::ipAddress(),
            self::$table::httpMethod(),
            self::$table::route(),
            self::$table::action(),
            self::$table::resource(),
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
            'index' => Pages\ListFingerprints::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'getOwner.name',
            'getOwner.email',
            'ip_address',
            'user_agent',
            'http_method',
            'route',
            'action',
            'status',
            'resource',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Resource' => $record->resource,
            'Action' => $record->action,
            'Status' => $record->status ? 'Allowed' : 'Denied',
        ];
    }
}
