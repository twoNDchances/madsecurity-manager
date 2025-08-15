<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!($value instanceof UploadedFile))
        {
            $fail("{$attribute} invalid.");
            return;
        }

        $contents = @file_get_contents($value->getRealPath());
        if ($contents === false || $contents === '')
        {
            $fail("Can't read the content of {$attribute}");
            return;
        }

        if (!mb_check_encoding($contents, 'UTF-8'))
        {
            $fail("{$attribute} have UTF-8 valid");
            return;
        }

        $anchorCount = substr_count($contents, '&') + substr_count($contents, '*');
        if ($anchorCount > 100)
        {
            $fail("{$attribute} has too much anchor/alias, 100 is maximum.");
            return;
        }

        try
        {
            Yaml::parse(
                $contents,
                Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE | Yaml::PARSE_DATETIME
            );
        }
        catch (ParseException $exception)
        {
            $fail('YAML không hợp lệ: '.$exception->getMessage());
            return;
        }
    }
}
