<?php

namespace App\Services;

use App\Filament\Resources\TagResource;
use App\Filament\Resources\TagResource\Pages\CreateTag;
use Filament\Support\Colors\Color;

class TagFieldService
{
    public static function setTags()
    {
        $former = [
            TagResource::main(true),
        ];
        return FilamentFormService::select('tags')
        ->relationship('tags', 'name')
        ->createOptionForm($former)
        ->searchable()
        ->multiple()
        ->preload();
    }

    public static function getTags()
    {
        $color = function($record, $state)
        {
            $tags = $record->tags()->pluck('color', 'name')->toArray();
            return Color::hex($tags[$state]);
        };
        return FilamentTableService::text('tags.name')
        ->badge()
        ->color($color)
        ->listWithLineBreaks()
        ->limitList(3)
        ->expandableLimitedList();
    }

    public static function tagIds()
    {
        return 'nullable|array';
    }

    public static function tagId()
    {
        return 'exists:tags,id';
    }

    public static function syncTags($validated, $resource)
    {
        if (!isset($validated['tag_ids']))
        {
            return;
        }
        $resource->tags()->sync($validated['tag_ids']);
    }
}
