<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Services\FilamentColumnService;
use App\Services\FilamentFormService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Privileges';

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
            self::information()->columns(2)->columnSpan(2),
            self::scope()->columnSpan(1),
        ]);
    }

    private static function information()
    {
        return Forms\Components\Section::make('User Information')
        ->schema([
            self::setName(),
            self::setEmail(),
            self::setPassword()->columnSpanFull(),
            self::forceVerification()->columnSpanFull(),
        ]);
    }

    private static function scope()
    {
        return Forms\Components\Section::make('User Scope')
        ->schema([
            self::setPolicies(),
            self::setActivation(),
            self::setImportant(),
        ]);
    }

    private static function setName()
    {
        $rules = [
            'required',
            'string',
            'max:255',
        ];
        return FilamentFormService::textInput(
            'name',
            'User Name',
            'Name',
            $rules
        )
        ->required();
    }

    private static function setEmail()
    {
        $rules = [
            'required',
            'string',
            'email',
            'max:255',
        ];
        return FilamentFormService::textInput(
            'email',
            'User Email',
            'user@email.com',
            $rules
        )
        ->required()
        ->unique(ignoreRecord: true)
        ->email();
    }

    private static function setPassword()
    {
        $condition = fn($livewire) => $livewire instanceOf CreateRecord;
        $length = fn($livewire) => $condition($livewire) ? 4 : null;
        $rules = [
            fn($livewire) => $condition($livewire) ? 'required' : 'nullable',
            fn($livewire) => $condition($livewire) ? 'min:4' :
            function ($attribute, $value, $fail)
            {
                if (!empty($value) && strlen($value) < 4)
                {
                    $fail("The {$attribute} must be at least 4 characters.");
                }
            },
            'string',
            'max:255',
        ];
        return FilamentFormService::textInput(
            'password',
            'User Password',
            'S3cr3tp@ssw0rd',
            $rules
        )
        ->required($condition)
        ->minLength($length)
        ->password()
        ->revealable()
        ->suffixAction(self::generatePassword());
    }

    private static function generatePassword()
    {
        $action = function($set)
        {
            $set('password', Str::random(12));
        };
        return Forms\Components\Actions\Action::make('password_creation')
        ->icon('heroicon-o-arrow-path')
        ->action($action);
    }

    private static function forceVerification()
    {
        $condition = fn($livewire) => $livewire instanceOf CreateRecord;
        $rules = [
            fn($livewire) => $condition($livewire) ? 'required' : 'nullable',
            'boolean',
        ];
        return FilamentFormService::toggle(
            'force_verification',
            'Force account verification',
            $rules
        )
        ->helperText('This account will receive an email for verification before use.')
        ->visible($condition);
    }

    private static function setPolicies()
    {
        return FilamentFormService::select(
            'policies',
            'User Policies',
            null,
        )
        ->relationship('policies', 'name')
        ->multiple()
        ->searchable()
        ->preload();
    }

    private static function setActivation()
    {
        $rules = [
            'required',
            'boolean',
        ];
        return FilamentFormService::toggle(
            'active',
            'Active account',
            $rules,
        )
        ->helperText('Turn on to start using this account.')
        ->default(true);
    }

    private static function setImportant()
    {
        $rules = [
            'required',
            'boolean',
        ];
        return FilamentFormService::toggle(
            'important',
            'Important account',
            $rules,
        )
        ->helperText('This account will be on par with the root account.');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
