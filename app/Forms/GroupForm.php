<?php

namespace App\Forms;

use App\Filament\Resources\DefenderResource;
use App\Filament\Resources\RuleResource;
use App\Filament\Resources\RuleResource\Pages\CreateRule;
use App\Forms\Actions\GroupAction;
use App\Models\Group;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\GUI\GroupValidator;

class GroupForm
{
    private static $validator = GroupValidator::class;

    private static $action = GroupAction::class;

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
        ->suffixAction(self::$action::generateName());
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
        ->preload()
        ->helperText('Interact with Defender to Apply and Revoke Rules, matching AND logic by grouping multiple Rules together');
        if ($form)
        {
            $former = [
                RuleResource::main(false, true),
            ];
            $creator = function(array $data)
            {
                $rule = CreateRule::callByStatic($data);
                if (isset($data['groups']))
                {
                    $rule->groups()->sync($data['groups']);
                }
                if (isset($data['tags']))
                {
                    $rule->tags()->sync($data['tags']);
                }
                return $rule->id;
            };
            $ruleField = $ruleField
            ->createOptionForm($former)
            ->createOptionUsing($creator);
        }
        return $ruleField;
    }

    public static function defenders($form = true)
    {
        $defenderField = FilamentFormService::select(
            'defenders',
            null,
            self::$validator::defenders(),
        )
        ->relationship('defenders', 'url')
        ->multiple()
        ->searchable()
        ->preload();
        if ($form)
        {
            $former = [
                DefenderResource::main(false, false),
            ];
            $defenderField = $defenderField
            ->createOptionForm($former);
        }
        return $defenderField;
    }

    public static function tags($dehydrated = false)
    {
        return TagFieldService::setTags($dehydrated);
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
