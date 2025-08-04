<?php

namespace App\Validators\API;

class TargetValidator
{
    private static array $phases = [
        1 => '1. Header Request',
        2 => '2. Body Request',
        3 => '3. Header Response',
        4 => '4. Body Response',
    ];

    private static array $types = [
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

    private static array $datatypes = [
        'array' => 'Array',
        'number' => 'Number',
        'string' => 'String',
    ];

    private static array $engines = [
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

    private static array $hashes = [
        'md5' => 'MD5',
        'sha1' => 'SHA128',
        'sha256' => 'SHA256',
        'sha512' => 'SHA512',
    ];

    private static function name($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255';
    }

    private static function alias($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255|alpha_dash|unique:targets,alias' . ($id ? ",$id" : '');
    }

    private static function datatype($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|string|in:' . implode(
            ',',
            array_keys(self::$datatypes),
        );
    }

    private static function wordlistId()
    {
        return 'required_if:datatype,array|integer|exists:wordlists,id';
    }

    private static function engine($request)
    {
        return [
            'nullable',
            'string',
            function ($attribute, $value, $fail) use ($request)
            {
                $datatype = $request->input('datatype');
                if (!isset(self::$engines[$datatype]))
                {
                    $fail("Invalid $attribute selected.");
                    return;
                }
                if (!array_key_exists($value, self::$engines[$datatype]))
                {
                    $fail("The selected engine '$value' is not valid for Datatype '$datatype'.");
                    return;
                }
            },
        ];
    }

    
}
