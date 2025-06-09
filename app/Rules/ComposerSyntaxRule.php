<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ComposerSyntaxRule implements ValidationRule
{
    protected array $parsed = [];
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try
        {
            $parsed = Yaml::parse($value);
            if (!is_array($parsed))
            {
                
            }
        }
        catch (ParseException $parseException)
        {
            $fail($parseException->getMessage());
        }
    }
}
