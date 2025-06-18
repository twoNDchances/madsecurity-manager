<?php

namespace App\Forms;

use App\Services\FilamentFormService;
use App\Services\TagFieldService;
use App\Validators\RuleValidator;

class RuleForm
{
    private static $validator = RuleValidator::class;

    //

    public static function tags()
    {
        return TagFieldService::setTags();
    }
}
