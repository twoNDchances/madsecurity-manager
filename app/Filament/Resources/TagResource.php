<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Models\Tag;
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

    protected static ?string $navigationGroup = 'Managements';

    protected static ?int $navigationSort = 5;

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
            self::information()->columns(2)->columnSpan(2),
        ]);
    }

    private static function definition()
    {
        return Forms\Components\Section::make('Tag Definition')
        ->schema([
            self::setTaggable()
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('Tag Information')
        ->schema([
            self::setName(),
            self::setColor(),
            self::setDescription()->columnSpanFull(),
        ]);
    }

    private static function setTaggable()
    {
        // return Forms\Components\MorphToSelect::make('');
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
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
