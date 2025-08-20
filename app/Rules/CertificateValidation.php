<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CertificateValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $bytes = file_get_contents($value->getRealPath());
        if (!$bytes)
        {
            $fail("Invalid {$attribute}.");
            return;
        }
        $content = $bytes;
        $looksLikePEM = str_contains($content, '-----BEGIN CERTIFICATE-----');
        if (!$looksLikePEM)
        {
            $pem = "-----BEGIN CERTIFICATE-----\n"
                . chunk_split(base64_encode($content), 64, "\n")
                . "-----END CERTIFICATE-----\n";
            $content = $pem;
        }
        $x509 = @openssl_x509_read($content);
        if ($x509 == false)
        {
            $fail("The {$attribute} field must contain a valid certificate (PEM base64).");
            return;
        }
    }
}
