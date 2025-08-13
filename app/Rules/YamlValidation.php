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
        if (!($value instanceof UploadedFile)) {
            $fail('Tệp không hợp lệ.');
            return;
        }

        $contents = @file_get_contents($value->getRealPath());
        if ($contents === false || $contents === '') {
            $fail('Không thể đọc nội dung YAML hoặc tệp rỗng.');
            return;
        }

        if (!mb_check_encoding($contents, 'UTF-8')) {
            $fail('Tệp YAML phải là UTF-8 hợp lệ.');
            return;
        }

        // Hạn chế anchor/alias để tránh YAML bomb
        // $anchorCount = substr_count($contents, '&') + substr_count($contents, '*');
        // if ($anchorCount > $this->maxAnchors) {
        //     $fail('Tệp YAML có quá nhiều anchor/alias.');
        //     return;
        // }

        try {
            $data = Yaml::parse(
                $contents,
                Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE | Yaml::PARSE_DATETIME
            );
        } catch (ParseException $e) {
            $fail('YAML không hợp lệ: '.$e->getMessage());
            return;
        }
    }
}
