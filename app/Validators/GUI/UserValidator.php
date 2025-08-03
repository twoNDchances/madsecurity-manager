<?php

namespace App\Validators\GUI;

use Filament\Resources\Pages\CreateRecord;

class UserValidator
{
    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
        ];
    }

    public static function email()
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            function($record)
            {
                if ($record)
                {
                    return "unique:users,email,$record->id";
                }
                return 'unique:users,email';
            },
        ];
    }

    public static function password()
    {
        $condition = fn($livewire) => $livewire instanceOf CreateRecord;
        return [
            fn($livewire) => $condition($livewire) ? 'required' : 'nullable',
            'string',
            'min:4',
            'max:255',
        ];
    }

    public static function verification()
    {
        $condition = fn($livewire) => $livewire instanceOf CreateRecord;
        return [
            fn($livewire) => $condition($livewire) ? 'required' : 'nullable',
            'boolean',
        ];
    }

    public static function policies()
    {
        return [
            'nullable',
            'array',
            'exists:policies,id',
        ];
    }

    public static function activation()
    {
        return [
            'required',
            'boolean',
        ];
    }

    public static function important()
    {
        return [
            'required',
            'boolean',
        ];
    }

    public static function tokens()
    {
        return [
            'nullable',
            'array',
            'exists:tokens,id',
        ];
    }
}
