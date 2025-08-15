<?php

namespace App\Forms;

use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\GUI\AssetValidator;

class AssetForm
{
    private static $validator = AssetValidator::class;

    public static function name()
    {
        return FilamentFormService::textInput(
            'name',
            null,
            'Asset Name',
            self::$validator::name(),
        )
        ->required()
        ->unique(ignoreRecord: true);
    }

    public static function path()
    {
        return FilamentFormService::fileUpload(
            'path',
            'Asset',
            self::$validator::definitionAsset(),
        )
        ->required()
        ->maxSize(1048576)
        ->acceptedFileTypes(
            array_merge(
                array_map(fn($item) => ".$item", self::$validator::$mimes),
                self::$validator::$mimeTypes,
            ),
        )
        ->directory('assets');
    }

    public static function tags()
    {
        return TagFieldService::setTags();
    }

    public static function totalAsset()
    {
        return FilamentFormService::textInput(
            'total_asset',
            'Total Asset',
            'Asset Counter',
        )
        ->disabled()
        ->integer();
    }

    public static function totalResource()
    {
        return FilamentFormService::textInput(
            'total_resource',
            'Total Resource',
            'Resource Counter',
        )
        ->disabled()
        ->integer();
    }

    public static function failResource()
    {
        return FilamentFormService::textInput(
            'fail_resource',
            'Fail Resource',
            'Fail Counter',
        )
        ->disabled()
        ->integer();
    }

    public static function output()
    {
        return FilamentFormService::textarea(
            'output',
            'Analysis Results',
        )
        ->rows(12)
        ->disabled();
    }
}
