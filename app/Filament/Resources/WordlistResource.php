<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages\CreateTag;
use App\Filament\Resources\WordlistResource\Pages;
use App\Models\Wordlist;
use App\Services\FilamentColumnService;
use App\Services\FilamentFormService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Table;

class WordlistResource extends Resource
{
    protected static ?string $model = Wordlist::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-arrow-down';

    protected static ?string $navigationGroup = 'Managements';

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
            self::information()->columns(2)->columnSpan(1),
            self::definition()->columnSpan(1),
        ]);
    }

    public static function information()
    {
        return Forms\Components\Section::make('Wordlist Information')
        ->schema([
            self::setName(),
            self::setAlias(),
            self::setTags()->columnSpanFull(),
            self::setDescription()->columnSpanFull(),
        ]);
    }

    public static function definition()
    {
        return Forms\Components\Section::make('Wordlist Definition')
        ->schema([
            self::setContent(),
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
            'Wordlist Name',
            $rules,
        )
        ->required();
    }

    private static function setAlias()
    {
        $rules = [
            'required',
            'max:255',
            'string',
            'alpha_dash',
        ];
        return FilamentFormService::textInput(
            'alias',
            null,
            'Wordlist Alias',
            $rules,
        )
        ->alphaDash()
        ->required()
        ->unique(ignoreRecord: true);
    }

    private static function setTags()
    {
        $former = [
            TagResource::main(),
        ];
        $creator = fn($data) => CreateTag::callByStatic($data)->id;
        return FilamentFormService::select('tags')
        ->relationship('tags', 'name')
        ->createOptionForm($former)
        ->createOptionUsing($creator)
        ->searchable()
        ->multiple()
        ->preload();
    }

    private static function setDescription()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Some description for this Wordlist'
        );
    }

    private static function setContent()
    {
        $state = function ($record, $set)
        {
            if ($record) {
                $set('content', $record->words()->pluck('content')->implode("\n"));
            }
        };
        return FilamentFormService::textarea(
            'content',
            null,
            'End a word with a new line'
        )
        ->afterStateHydrated($state);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            self::getName(),
            self::getAlias(),
            self::getCounter(),
            self::getTags(),
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

    private static function getName()
    {
        return FilamentColumnService::text('name');
    }

    private static function getAlias()
    {
        return FilamentColumnService::text('alias');
    }

    private static function getCounter()
    {
        return FilamentColumnService::text('words_count')->counts('words');
    }

    private static function getTags()
    {
        $color = function($record, $state)
        {
            $tags = $record->tags()->pluck('color', 'name')->toArray();
            return Color::hex($tags[$state]);
        };
        return FilamentColumnService::text('tags.name')
        ->badge()
        ->color($color)
        ->listWithLineBreaks()
        ->limitList(3)
        ->expandableLimitedList();
    }

    private static function getOwner()
    {
        return FilamentColumnService::text('getOwner.email', 'Created by');
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
