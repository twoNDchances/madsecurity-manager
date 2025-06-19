<?php

namespace App\Forms;

use App\Filament\Resources\RuleResource;
use App\Filament\Resources\RuleResource\Pages\CreateRule;
use App\Models\Group;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\GroupValidator;
use Filament\Forms\Components\Actions\Action;

class GroupForm
{
    private static $validator = GroupValidator::class;

    public static function name()
    {
        return FilamentFormService::textInput(
            'name',
            null,
            'Group Name',
            self::$validator::name(),
        )
        ->required()
        ->unique(ignoreRecord: true)
        ->suffixAction(self::generateName());
    }

    public static function generateName()
    {
        $action = function($set)
        {
            $set('name', 'group-' . now()->timestamp);
        };
        return Action::make('generate_name')
        ->icon('heroicon-o-arrow-path')
        ->action($action);
    }

    public static function executionOrder()
    {
        $default = Group::query()->max('execution_order') + 1;
        return FilamentFormService::textInput(
            'execution_order',
            'Execution Order',
            'Group Execution Order',
            self::$validator::executionOrder(),
        )
        ->required()
        ->integer()
        ->minValue(1)
        ->default($default);
    }

    public static function level()
    {
        return FilamentFormService::textInput(
            'level',
            null,
            'Group Level',
            self::$validator::level(),
        )
        ->required()
        ->integer()
        ->minValue(1)
        ->default(1);
    }

    public static function rules($form = true)
    {
        $ruleField = FilamentFormService::select(
            'rules',
            null,
            self::$validator::rules(),
        )
        ->relationship('rules', 'alias')
        ->multiple()
        ->searchable()
        ->preload();
        if ($form)
        {
            $former = [
                RuleResource::main(false),
            ];
            $creator = fn($data) => CreateRule::callByStatic($data)->id;
            $ruleField = $ruleField
            ->createOptionForm($former)
            ->createOptionUsing($creator);
        }
        return $ruleField;
    }

    public static function tags()
    {
        return TagFieldService::setTags();
    }

    public static function description()
    {
        return FilamentFormService::textarea(
            'description',
            null,
            'Some Description about this Group'
        )
        ->rules(self::$validator::description());
    }
}
