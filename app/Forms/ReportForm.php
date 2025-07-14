<?php

namespace App\Forms;

use App\Forms\Actions\ReportAction;
use App\Models\Target;
use App\Services\FilamentFormService;
use Filament\Forms\Components\Fieldset;

class ReportForm
{
    private static $action = ReportAction::class;

    public static function defenderName()
    {
        $state = fn($record, $set) => $set('defender_name', $record->getDefender->name);
        return FilamentFormService::textInput(
            'defender_name',
            'Defender Name',
        )
        ->afterStateHydrated($state);
    }

    public static function defenderUrl()
    {
        $state = fn($record, $set) => $set('defender_url', $record->getDefender->url . $record->getDefender->health);
        $actions = [
            self::$action::pingDefender(),
        ];
        return FilamentFormService::textInput(
            'defender_url',
            'Defender URL',
        )
        ->afterStateHydrated($state)
        ->suffixActions($actions);
    }

    public static function time()
    {
        return FilamentFormService::textInput('time');
    }

    public static function userAgent()
    {
        return FilamentFormService::textInput('user_agent', 'User Agent');
    }

    public static function clientIp()
    {
        return FilamentFormService::textInput('client_ip', 'Client IP');
    }

    public static function method()
    {
        return FilamentFormService::textInput('method');
    }

    public static function path()
    {
        return FilamentFormService::textInput('path');
    }

    public static function output()
    {
        $state = fn($record, $set) => $set(
            'output',
            implode(
                "\n\n================================================================================\n\n",
                $record->output,
            ),
        );
        return FilamentFormService::textarea('output')
        ->afterStateHydrated($state);
    }

    public static function targets()
    {
        $schema = [
            self::targetName()->columnSpan(3),
            self::targetAlias()->columnSpan(3),
            self::targetPhase()->columnSpan(4),
            self::targetType()->columnSpan(2),
            self::targetSuperior()->columnSpan(3),
            self::targetWordlist()->columnSpan(3),
            self::targetDatatype()->columnSpan(3),
            self::targetFinalDatatype()->columnSpan(3),
            Fieldset::make('Transformation')
            ->schema([
                self::targetEngine(),
                self::targetEngineConfiguration(),
            ])
            ->columns(2)
            ->columnSpanFull(),
        ];
        $state = function($record, $set)
        {
            $targetIds = $record->target_ids;
            $transformed = collect($targetIds)
            ->map(function($id)
            {
                $target = Target::find($id);
                if (!$target)
                {
                    return [];
                }
                return [
                    'name' => $target->name,
                    'alias' => $target->alias,
                    'phase' => $target->phase,
                    'type' => $target->type,
                    'target_id' => $target->getSuperior?->name,
                    'wordlist_id' => $target->getWordlist?->alias,
                    'datatype' => $target->datatype,
                    'final_datatype' => $target->final_datatype,
                    'engine' => $target?->engine,
                    'engine_configuration' => $target?->engine_configuration,
                ];
            })
            ->toArray();
            $set('targets', $transformed);
        };
        return FilamentFormService::repeater('targets', $schema)
        ->afterStateHydrated($state);
    }

    private static $targetForm = TargetForm::class;

    private static function targetName()
    {
        return FilamentFormService::textInput('name');
    }

    private static function targetAlias()
    {
        return FilamentFormService::textInput('alias');
    }

    private static function targetPhase()
    {
        return self::$targetForm::phase();
    }

    private static function targetType()
    {
        return self::$targetForm::type();
    }

    private static function targetSuperior()
    {
        return FilamentFormService::textInput('target_id', 'Referer');
    }

    private static function targetWordlist()
    {
        return FilamentFormService::textInput('wordlist_id', 'Wordlist Alias');
    }

    private static function targetEngine()
    {
        return FilamentFormService::textInput('engine');
    }

    private static function targetEngineConfiguration()
    {
        return FilamentFormService::textInput('engine_configuration', 'Configuration');
    }

    private static function targetDatatype()
    {
        return self::$targetForm::datatype();
    }

    private static function targetFinalDatatype()
    {
        return self::$targetForm::finalDatatype();
    }

    private static $ruleForm = RuleForm::class;

    public static function ruleName()
    {
        $state = fn($record, $set) => $set('name', $record->getRule?->name);
        return self::$ruleForm::name()
        ->afterStateHydrated($state);
    }

    public static function ruleAlias()
    {
        $state = fn($record, $set) => $set('alias', $record->getRule?->alias);
        return self::$ruleForm::alias()
        ->afterStateHydrated($state);
    }

    public static function rulePhase()
    {
        $state = fn($record, $set) => $set('phase', $record->getRule?->phase);
        return self::$ruleForm::phase()
        ->afterStateHydrated($state);
    }

    public static function ruleTarget()
    {
        $state = fn($record, $set) => $set('target', $record->getRule?->getTarget?->name);
        return FilamentFormService::textInput('target', 'Target Name')
        ->afterStateHydrated($state);
    }

    public static function ruleComparator()
    {
        $state = fn($record, $set) => $set('comparator', $record->getRule?->comparator);
        return FilamentFormService::textInput('comparator')
        ->afterStateHydrated($state);
    }

    public static function ruleInverse()
    {
        $state = fn($record, $set) => $set('inverse', $record->getRule?->inverse);
        return self::$ruleForm::inverse()
        ->afterStateHydrated($state);
    }

    public static function ruleValue()
    {
        $state = fn($record, $set) => $set('value', $record->getRule?->value);
        return self::$ruleForm::value()
        ->afterStateHydrated($state);
    }

    public static function ruleWordlist()
    {
        $state = fn($record, $set) => $set('wordlist', $record->getWordlist?->alias);
        return FilamentFormService::textInput('wordlist', 'Wordlist Alias')
        ->afterStateHydrated($state);
    }

    public static function ruleAction()
    {
        $state = fn($record, $set) => $set('action', $record->getRule?->action);
        return self::$ruleForm::action()
        ->afterStateHydrated($state);
    }

    public static function ruleActionConfiguration()
    {
        $state = fn($record, $set) => $set('action_configuration', $record->getRule?->action_configuration);
        return FilamentFormService::textInput('action_configuration', 'Configuration')
        ->afterStateHydrated($state);
    }
}
