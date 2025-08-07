<?php

namespace App\Validators\API;

use App\Models\Target;
use App\Models\Wordlist;
use App\Services\TagFieldService;

class TargetValidator
{
    public static function build($request, $required = true, $id = null)
    {
        return [
            'name' => self::name($required),
            'alias' => self::alias($required, $id),
            'datatype' => self::datatype($required),
            'wordlist_id' => self::wordlistId(),
            'engine' => self::engine($request),
            'indexOf' => self::indexOf($request),
            'number' => self::number(),
            'hash' => self::hash(),
            'tag_ids' => TagFieldService::tagIds(),
            'tag_ids.*' => TagFieldService::tagId(),
            'description' => self::description(),
            'phase' => self::phase($required),
            'type' => self::type($request, $required),
            'superior' => self::superior($request, $id),
        ];
    }

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

    private static function name($required = true)
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

    private static function indexOf($request)
    {
        return [
            'required_if:engine,indexOf',
            'integer',
            'min:0',
            function ($attribute, $value, $fail) use ($request)
            {
                $targetId = $request->input('target_id');
                $wordlist = null;
                if ($targetId)
                {
                    $target = Target::find($targetId);
                    if ($target->immutable)
                    {
                        return;
                    }
                    $root = Target::getRoot($target);
                    $wordlist = Wordlist::find($root->wordlist_id);
                }

                if ($targetId)
                {
                    $wordlist = Wordlist::find($targetId);
                }

                $counter = $wordlist->words()->count();
                if ($counter == 0 || ($counter - 1) < $value)
                {
                    $fail("The $attribute has crossed the limit, total $counter");
                    return;
                }
                $fail("The Wordlist required for $attribute");
            },
        ];
    }

    private static function number()
    {
        $requiredIf = [
            'addition',
            'subtraction',
            'multiplication',
            'division',
            'powerOf',
            'remainder',
        ];
        return 'required_if:engine,' . implode(',', $requiredIf) . '|numeric';
    }

    private static function hash()
    {
        return 'required_if:engine,hash|in:' . implode(',', array_keys(self::$hashes));
    }

    private static function description()
    {
        return 'nullable|string';
    }

    private static function phase($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|integer|in:' . implode(',', array_keys(self::$phases));
    }

    private static function type($request, $required = true)
    {
        return [
            $required ? 'required' : 'sometimes',
            'string',
            function ($attribute, $value, $fail) use ($request)
            {
                $phase = (int) $request->input('phase');
                if (!isset(self::$types[$phase]))
                {
                    $fail("Invalid $attribute selected.");
                    return;
                }
                if (!array_key_exists($value, self::$types[$phase]))
                {
                    $fail("Invalid $attribute selected for Phase '$phase'.");
                    return;
                }
            },
        ];
    }

    private static function superior($request, $id = null)
    {
        return [
            'required_if:type,target',
            'integer',
            function($attribute, $value, $fail) use ($request, $id)
            {
                $target = Target::find($value);
                if (!$target)
                {
                    $fail("The {$attribute} is invalid");
                    return;
                }
                $phase = (int) $request->input('phase');
                if ($target->phase != $phase)
                {
                    $fail("The phase of {$attribute} mismatch");
                    return;
                }
                if ($id)
                {
                    $target = Target::find($id);
                    if ($target->id == $value)
                    {
                        $fail("The {$attribute} can't reference to itself");
                        return;
                    }
                    if (Target::getRoot($target)->id == $target->id)
                    {
                        $fail("The {$attribute} cannot be selected because it creates a circular reference to itself (via root)");
                        return;
                    }
                }
            },
        ];
    }
}
