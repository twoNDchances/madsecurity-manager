<?php

namespace App\Validators\GUI;

use App\Models\Target;
use App\Models\Wordlist;

class TargetValidator
{
    public static array $phases = [
        1 => '1. Header Request',
        2 => '2. Body Request',
        3 => '3. Header Response',
        4 => '4. Body Response',
    ];

    public static array $types = [
        1 => [
            'target' => 'Target',
            'getter' => 'Getter',
            'header' => 'Header',
            'url.args' => 'URL Arguments',
        ],
        2 => [
            'target' => 'Target',
            'getter' => 'Getter',
            'body' => 'Body',
            'file' => 'File',
        ],
        3 => [
            'target' => 'Target',
            'getter' => 'Getter',
            'header' => 'Header',
        ],
        4 => [],
    ];

    public static array $datatypes = [
        'array' => 'Array',
        'number' => 'Number',
        'string' => 'String',
    ];

    public static array $engines = [
        'array' => [
            'indexOf' => 'Index Of',
        ],
        'number' => [
            'addition' => 'Addition',
            'subtraction' => 'Subtraction',
            'multiplication' => 'Multiplication',
            'division' => 'Division',
            'powerOf' => 'Power Of',
            'remainder' => 'Remainder',
        ],
        'string' => [
            'lower' => 'Lower',
            'upper' => 'Upper',
            'capitalize' => 'Capitalize',
            'trim' => 'Trim',
            'trimLeft' => 'Trim Left',
            'trimRight' => 'Trim Right',
            'removeWhitespace' => 'Remove Whitespace',
            'length' => 'Length',
            'hash' => 'Hash',
        ],
    ];

    public static array $hashes = [
        'md5' => 'MD5',
        'sha1' => 'SHA128',
        'sha256' => 'SHA256',
        'sha512' => 'SHA512',
    ];

    public static function name()
    {
        return [
            'required',
            'string',
            'max:255',
        ];
    }

    public static function alias()
    {
        return [
            'required',
            'string',
            'max:255',
            'alpha_dash',
            function($record)
            {
                if ($record)
                {
                    return "unique:targets,alias,$record->id";
                }
                return 'unique:targets,alias';
            },
        ];
    }

    public static function datatype()
    {
        return [
            'required',
            'string',
            'in:' . implode(',', array_keys(self::$datatypes)),
        ];
    }

    public static function wordlist()
    {
        return [
            'required_if:datatype,array',
            'integer',
            'exists:wordlists,id',
        ];
    }

    public static function engine()
    {
        return [
            'nullable',
            'string',
            fn($get) => 'in:' . implode(',', array_keys(self::$engines[$get('datatype')])),
        ];
    }

    public static function indexOf()
    {
        return [
            'required_if:engine,indexOf',
            'integer',
            'min:0',
            fn($get) => function($attribute, $value, $fail) use ($get)
            {
                $wordlist = null;
                if ($get('target_id'))
                {
                    $target = Target::find($get('target_id'));
                    if ($target->immutable)
                    {
                        return;
                    }
                    $root = Target::getRoot($target);
                    $wordlist = Wordlist::find($root->wordlist_id);
                }

                if ($get('wordlist_id'))
                {
                    $wordlist = Wordlist::find($get('wordlist_id'));
                }

                $counter = $wordlist->words()->count();
                if ($counter == 0 || ($counter - 1) < $value)
                {
                    $fail("The {$attribute} has crossed the limit, total {$counter}");
                    return;
                }
                $fail("The Wordlist required for {$attribute}");
            },
        ];
    }

    public static function number()
    {
        $requiredIf = [
            'engine',
            'addition',
            'subtraction',
            'multiplication',
            'division',
            'powerOf',
            'remainder',
        ];
        return [
            'required_if:' . implode(',', $requiredIf),
            'numeric',
        ];
    }

    public static function hash()
    {
        return [
            'required_if:engine,hash',
            'in:' . implode(',', array_keys(self::$hashes)),
        ];
    }

    public static function description()
    {
        return [
            'nullable',
            'string',
        ];
    }

    public static function phase()
    {
        return [
            'required',
            'integer',
            'in:' . implode(',', array_keys(self::$phases)),
        ];
    }

    public static function type()
    {
        return [
            'required',
            'string',
            fn($get) => 'in:' . implode(',', array_keys(self::$types[(int) $get('phase')]))
        ];
    }

    public static function superior()
    {
        return [
            'required_if:type,target',
            'integer',
            fn($record, $get) => function($attribute, $value, $fail) use ($record, $get)
            {
                $target = Target::find($value);
                if (!$target)
                {
                    $fail("The {$attribute} is invalid");
                    return;
                }
                if ($target->phase != $get('phase'))
                {
                    $fail("The phase of {$attribute} mismatch");
                    return;
                }
                if ($record)
                {
                    if ($record->id == $value)
                    {
                        $fail("The {$attribute} can't reference to itself");
                        return;
                    }

                    if (Target::getRoot($record)->id == $record->id)
                    {
                        $fail("The {$attribute} cannot be selected because it creates a circular reference to itself (via root)");
                        return;
                    }
                }
            },
        ];
    }
}
