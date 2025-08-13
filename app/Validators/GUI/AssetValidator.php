<?php

namespace App\Validators\GUI;

use App\Rules\YamlValidation;

class AssetValidator
{
    public static $mimes = [
        'yml',
        'yaml',
    ];

    public static $mimeTypes = [
        'text/yaml',
        'text/x-yaml',
        'application/x-yaml',
        'text/plain',
    ];

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
                    return "unique:assets,name,$record->id";
                }
                return 'unique:assets,name';
            }
        ];
    }

    public static function definitionAsset()
    {
        return [
            'required',
            'file',
            'max:1048576',
            'mimes:' . implode(',' , self::$mimes),
            'mimetypes:' . implode(',', self::$mimeTypes),
            new YamlValidation(),
        ];
    }
}
