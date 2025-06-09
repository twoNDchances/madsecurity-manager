<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComposerResource\Pages;
use App\Models\Composer;
use App\Rules\ComposerSyntaxRule;
use App\Services\FilamentColumnService;
use App\Services\FilamentFormService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComposerResource extends Resource
{
    protected static ?string $model = Composer::class;

    protected static ?string $navigationIcon = 'heroicon-o-musical-note';

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
            self::description()->columns(2)->columnSpan(1),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Tabs::make()
        ->schema([
            Forms\Components\Tabs\Tab::make('Definition')
            ->schema([
                self::setYamlByEditor(),
            ]),
            Forms\Components\Tabs\Tab::make('Manifest')
            ->schema([
                self::setYamlByFile(),
            ]),
        ]);
    }

    private static function description()
    {
        return Forms\Components\Tabs::make()
        ->schema([
            Forms\Components\Tabs\Tab::make('Overview')
            ->schema([
                self::setOutput()->columnSpanFull(),
            ]),
            Forms\Components\Tabs\Tab::make('Detail')
            ->schema([
                self::setPass(),
                self::setFall(),
                self::setResources()->columnSpanFull(),
            ]),
        ]);
    }

    private static function setYamlByEditor()
    {
        $composerSyntax = new ComposerSyntaxRule();
        $rules = [
            'required',
            'string',
            $composerSyntax,
        ];
        $state = function($set)
        {

        };
        return FilamentFormService::textarea(
            'yaml',
            'YAML',
            'Resource Definitions here'
        )
        ->required()
        ->rows(18)
        ->rules($rules)
        ->afterStateUpdated($state);
    }

    private static function setYamlByFile()
    {
        return FilamentFormService::fileUpload(
            'yaml',
            'YAML',
        );
    }

    private static function setOutput()
    {
        return FilamentFormService::textarea(
            'output',
            null,
            'Output of Resource Processor'
        )
        ->rows(18)
        ->readOnly();
    }

    private static function setPass()
    {
        $rules = [
            'nullable',
            'integer',
        ];
        return FilamentFormService::textInput(
            'pass',
            'Pass Score',
            null,
            $rules,
        )
        ->readOnly()
        ->integer()
        ->default(0);
    }

    private static function setFall()
    {
        $rules = [
            'nullable',
            'integer',
        ];
        return FilamentFormService::textInput(
            'fall',
            'Fall Score',
            null,
            $rules,
        )
        ->readOnly()
        ->integer()
        ->default(0);
    }

    private static function setResources()
    {
        $options = Composer::getModels();
        return FilamentFormService::checkboxList(
            'resources',
            null,
            $options,
        )
        ->columns(4)
        ->disabled();
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
