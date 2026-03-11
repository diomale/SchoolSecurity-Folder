<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip reCAPTCHA validation in local development
        if (app()->environment('local')) {
            return;
        }

        // 1. Send the token ($value) to Google
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $value,
        ]);
        /** @var \Illuminate\Http\Client\Response $response */
        $data = $response->json();

        // 2. Logic: Must be successful AND score must be "human enough" (0.5+)
        if (!$data['success'] || $data['score'] < 0.5) {
            $fail('The security check failed. Please try again.');
        }
    }
}
