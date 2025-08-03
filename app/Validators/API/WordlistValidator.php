<?php

namespace App\Validators\API;

use App\Services\TagFieldService;

class WordlistValidator
{
    public static function build($required = true, $id = null)
    {
        return [
            'name' => self::name($required),
            'alias' => self::alias($required, $id),
            'tag_ids' => TagFieldService::tagIds(),
            'tag_ids.*' => TagFieldService::tagId(),
            'description' => self::description(),
            'words' => self::words(),
            'words.*' => self::word(),
        ];
    }

    private static function name($required = true)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255';
    }

    private static function alias($required = true, $id = null)
    {
        return ($required ? 'required' : 'sometimes') . '|string|max:255|alpha_dash|unique:wordlists,alias' . ($id ? ",$id" : '');
    }

    private static function description()
    {
        return 'nullable|string';
    }

    private static function words()
    {
        return 'nullable|array';
    }

    private static function word()
    {
        return 'string';
    }
}
