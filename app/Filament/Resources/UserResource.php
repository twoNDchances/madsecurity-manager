<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Services\AuthenticationService;
use App\Services\FilamentColumnService;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            self::setTags()->columnSpanFull(),
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
            null,
            'User Name',
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
            null,
            'User Email',
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
            null,
            'User Password',
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

    private static function setTags()
    {
        return TagFieldService::setTags();
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
        $rules = [
            'nullable',
            Rule::exists('policies', 'id'),
        ];
        return FilamentFormService::select(
            'policies',
            'Policies',
            null,
            $rules,
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
            self::getName(),
            self::getEmail(),
            self::getActivation(),
            self::getVerified(),
            self::getPolicies(),
            self::getTags(),
            self::getOwner(),
        ])
        ->filters([
            //
        ])
        ->actions([
            FilamentColumnService::actionGroup(
                delete: false,
                more: [
                    FilamentColumnService::deleteUserAction(),
                ]
            ),
        ])
        ->bulkActions([
            FilamentColumnService::deleteUserBulkAction(),
        ]);
    }

    private static function getName()
    {
        return FilamentColumnService::text('name', null);
    }

    private static function getEmail()
    {
        return FilamentColumnService::text('email', null);
    }

    private static function getActivation()
    {
        $user = AuthenticationService::get();
        if (AuthenticationService::can($user, 'user', 'update'))
        {
            return FilamentColumnService::toggle('active', 'Activated');
        }
        return FilamentColumnService::icon('active', 'Activated');
    }

    private static function getVerified()
    {
        return FilamentColumnService::icon('email_verified_at', 'Verified')->boolean();
    }

    private static function getPolicies()
    {
        return FilamentColumnService::text('policies.name', 'Policies')
        ->listWithLineBreaks()
        ->bulleted()
        ->limitList(5)
        ->expandableLimitedList();
    }

    private static function getTags()
    {
        return TagFieldService::getTags();
    }

    private static function getOwner()
    {
        return FilamentColumnService::text('getSuperior.email', 'Created by');
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
