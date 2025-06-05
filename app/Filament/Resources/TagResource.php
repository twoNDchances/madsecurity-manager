<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Models\Tag;
use App\Services\FilamentColumnService;
use App\Services\FilamentFormService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $navigationGroup = 'Classifies';

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
                self::setName(),
                self::setColor(),
            ])
            ->columnSpan(1),
            self::setDescription()->columnSpan(1),
        ]);
    }

    private static function setName()
    {
        $rules = [
            'required',
            'max:255',
            'string',
        ];
        return FilamentFormService::textInput(
            'name',
            null,
            'Tag Name',
            $rules
        )
        ->required()
        ->unique(ignoreRecord: true);
    }

    private static function setColor()
    {
        return FilamentFormService::colorPicker(
            'color',
            null,
        )
        ->required();
    }

    private static function setDescription()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Some description for this Tag'
        );
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::getName(),
            self::getColor(),
            self::getOwner(),
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

    private static function getName()
    {
        return FilamentColumnService::text('name');
    }

    private static function getColor()
    {
        return FilamentColumnService::color('color');
    }

    private static function getOwner()
    {
        return FilamentColumnService::text('getOwner.email', 'Created by');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
