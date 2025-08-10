<?php

namespace App\Validators\API;

class ReportValidator
{
    public static function build()
    {
        return [
            'defender_id' => self::defenderId(),
            'time' => self::time(),
            'output' => self::output(),
            'user_agent' => self::userAgent(),
            'client_ip' => self::clientIp(),
            'method' => self::method(),
            'path' => self::path(),
            'target_ids' => self::targetIds(),
            'target_ids.*' => self::targetId(),
            'rule_id' => self::ruleId(),
        ];
    }

    private static function defenderId()
    {
        return 'required|exists:defenders,id';
    }

    private static function time()
    {
        return 'required|date';
    }

    private static function output()
    {
        return 'required';
    }

    private static function userAgent()
    {
        return 'required|string|max:255';
    }

    private static function clientIp()
    {
        return 'required|string|ip';
    }

    private static function method()
    {
        return 'required|string|max:255';
    }

    private static function path()
    {
        return 'required|string|max:255';
    }

    private static function targetIds()
    {
        return 'required|array';
    }

    private static function targetId()
    {
        return 'exists:targets,id';
    }

    private static function ruleId()
    {
        return 'required|exists:rules,id';
    }
}
