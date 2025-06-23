<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DefenderResource\Pages;
use App\Filament\Resources\DefenderResource\RelationManagers\GroupsRelationManager;
use App\Forms\DefenderForm;
use App\Models\Defender;
use App\Tables\DefenderTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class DefenderResource extends Resource
{
    protected static ?string $model = Defender::class;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationGroup = 'Managements';

    private static $form = DefenderForm::class;

    private static $table = DefenderTable::class;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            self::main()->columns(3)->columnSpanFull(),
        ]);
    }

    public static function main($group = true, $owner = false)
    {
        $form = [
            self::information($group)->columns(2)->columnSpan(2),
            Forms\Components\Grid::make(1)
            ->schema([
                self::inspection(),
                self::authentication(),
            ])
            ->columns(1)
            ->columnSpan(1),
            self::console()->columnSpanFull(),
        ];
        if ($owner)
        {
            $form[] = self::$form::owner();
        }
        return Forms\Components\Grid::make(3)
        ->schema($form);
    }

    private static function information($group = true)
    {
        return Forms\Components\Section::make('Defender Information')
        ->schema([
            self::$form::name(),
            self::$form::groups($group),
            Forms\Components\Fieldset::make('Location')
            ->schema([
                self::$form::url()->columnSpanFull(),
                self::$form::path('health'),
                self::$form::path('sync'),
                self::$form::path('apply'),
                self::$form::path('revoke'),
            ])
            ->columns(4)
            ->columnSpanFull(),
            self::$form::tags()->columnSpan(1),
            self::$form::description()->columnSpan(1),
        ]);
    }

    private static function inspection()
    {
        return Forms\Components\Section::make('Defender Inspection')
        ->schema([
            self::$form::important(),
            self::$form::periodic(),
        ]);
    }

    private static function authentication()
    {
        return Forms\Components\Section::make('Defender Authentication')
        ->schema([
            self::$form::protection(),
            Forms\Components\Fieldset::make('Credential')
            ->schema([
                self::$form::noCredential(),
                self::$form::username(),
                self::$form::password(),
            ])
            ->columns(1),
        ]);
    }

    private static function console()
    {
        return Forms\Components\Section::make('Defender Console')
        ->schema([
            self::$form::output(),
        ])
        ->headerActions([self::$form::clearOutput()]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::$table::representation(),
            self::$table::periodic(),
            self::$table::lastStatus(),
            self::$table::groups(),
            self::$table::health(),
            self::$table::sync(),
            self::$table::apply(),
            self::$table::revoke(),
            self::$table::protection(),
            self::$table::tags(),
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
            GroupsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDefenders::route('/'),
            'create' => Pages\CreateDefender::route('/create'),
            'edit' => Pages\EditDefender::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
