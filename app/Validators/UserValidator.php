<?php

namespace App\Validators;

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
        ];
    }

    public static function password()
    {
        $condition = fn($livewire) => $livewire instanceOf CreateRecord;
        return [
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
}
