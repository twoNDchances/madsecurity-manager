<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DefenderResource\Pages;
use App\Filament\Resources\DefenderResource\RelationManagers\DecisionsRelationManager;
use App\Filament\Resources\DefenderResource\RelationManagers\GroupsRelationManager;
use App\Forms\DefenderForm;
use App\Models\Defender;
use App\Services\IdentificationService;
use App\Tables\DefenderTable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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

    public static function main($group = true, $decision = true, $dehydrated = false)
    {
        $condition = fn($livewire) => !$livewire instanceof CreateRecord;
        $active = function($livewire) use ($condition)
        {
            return $condition($livewire) ? 2 : 1;
        };
        return Forms\Components\Tabs::make()
        ->schema([
            Forms\Components\Tabs\Tab::make('Definition')
            ->icon('heroicon-o-server')
            ->schema([
                self::definition($group, $decision, $dehydrated),
            ]),

            Forms\Components\Tabs\Tab::make('Terminal')
            ->icon('heroicon-o-command-line')
            ->schema([
                self::console()->columns(4)->columnSpanFull(),
            ])
            ->visible($condition),
        ])
        ->contained(false)
        ->activeTab($active);
    }

    public static function definition($group = true, $decision = true, $dehydrated = false)
    {
        return Forms\Components\Grid::make(3)
        ->schema([
            self::information($group, $dehydrated)->columns(2)->columnSpan(2),
            Forms\Components\Grid::make(1)
            ->schema([
                self::reaction($decision),
                self::inspection(),
                self::authentication(),
            ])
            ->columns(1)
            ->columnSpan(1),
        ]);
    }

    private static function information($group = true, $dehydrated = false)
    {
        $condition = fn($livewire) => $livewire instanceof EditRecord;
        return Forms\Components\Section::make('Defender Information')
        ->schema([
            self::$form::name(),
            self::$form::groups($group),
            Forms\Components\Fieldset::make('Location')
            ->schema([
                self::$form::url()->columnSpanFull(),
                Forms\Components\Fieldset::make('General')
                ->schema([
                    self::$form::path('health'),
                    self::$form::method('health', 'post'),
                ])->columns(2),

                Forms\Components\Fieldset::make('Groups & Rules')
                ->schema([
                    self::$form::path('apply'),
                    self::$form::method('apply', 'patch'),
                    self::$form::path('revoke'),
                    self::$form::method('revoke', 'delete'),
                ])->columns(2)
                ->columnSpan(2),

                Forms\Components\Fieldset::make('Decisions')
                ->schema([
                    self::$form::path('implement'),
                    self::$form::method('implement', 'patch'),
                    self::$form::path('suspend'),
                    self::$form::method('suspend', 'delete'),
                ])->columns(2)
                ->columnSpan(2),
            ])
            ->columns(4)
            ->columnSpanFull(),
            self::$form::tags($dehydrated)->columnSpan(1),
            self::$form::description()->columnSpan(1),
        ])
        ->collapsible()
        ->collapsed($condition);
    }

    private static function reaction($decision = true)
    {
        $condition = fn($livewire) => $livewire instanceof EditRecord;
        return Forms\Components\Section::make('Defender Reaction')
        ->schema([
            self::$form::decisions($decision),
        ])
        ->columns(1)
        ->collapsible()
        ->collapsed($condition);
    }

    private static function inspection()
    {
        $condition = fn($livewire) => $livewire instanceof EditRecord;
        return Forms\Components\Section::make('Defender Inspection')
        ->schema([
            self::$form::important(),
            self::$form::periodic(),
        ])
        ->collapsible()
        ->collapsed($condition);
    }

    private static function authentication()
    {
        $condition = fn($livewire) => $livewire instanceof EditRecord;
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
        ])
        ->collapsible()
        ->collapsed($condition);
    }

    private static function console()
    {
        return Forms\Components\Section::make('Defender Console')
        ->schema([
            Forms\Components\Fieldset::make('Status')
            ->schema([
                self::$form::totalGroups(),
                self::$form::currentApplied(),
            ])
            ->columns(1)
            ->columnSpan(1),
            self::$form::output()
            ->columnSpan(3),
        ])
        ->headerActions([self::$form::clearOutput()])
        ->collapsible();
    }

    public static function table(Table $table): Table
    {
        $query = Defender::query();
        $user = IdentificationService::get();
        if (!$user->important)
        {
            $query->where('important',false);
        }
        return $table
        ->query($query)
        ->columns([
            self::$table::representation(),
            self::$table::periodic(),
            self::$table::lastStatus(),
            self::$table::groups(),
            self::$table::health(),
            self::$table::apply(),
            self::$table::revoke(),
            self::$table::implement(),
            self::$table::suspend(),
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
            DecisionsRelationManager::class,
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
        $user = IdentificationService::get();
        $model = static::getModel();
        return !$user->important ? $model::where('important', false)->count() : $model::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'url',
            'important',
            'periodic',
            'last_status',
            'health',
            'apply',
            'revoke',
            'output',
            'description',
            'protection',
            'username',
            'getOwner.name',
            'getOwner.email',
            'tags.name',
            'groups.name'
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'URL' => $record->url,
            'Total Groups' => $record->groups()->count(),
            'Current Applied' => $record->groups()->wherePivot('status', true)->count(),
        ];
    }
}
