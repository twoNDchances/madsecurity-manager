<?php

namespace App\Validators\GUI;

use Filament\Resources\Pages\CreateRecord;

class TokenValidator
{
    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
            function($record)
            {
                if ($record)
                {
                    return "unique:tokens,name,$record->id";
                }
                return 'unique:tokens,name';
            },
        ];
    }

    public static function value()
    {
        $condition = fn($livewire) => $livewire instanceOf CreateRecord;
        return [
            fn($livewire) => $condition($livewire) ? 'required' : 'nullable',
            'string',
            'min:48',
            'max:48',
            function($record)
            {
                if ($record)
                {
                    return "unique:tokens,value,$record->id";
                }
                return 'unique:tokens,value';
            },
        ];
    }

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

    public static function expiredAt()
    {
        return [
            'nullable',
            'date',
        ];
    }

    public static function users()
    {
        return [
            'nullable',
            'array',
            'exists:users,id',
        ];
    }
}
