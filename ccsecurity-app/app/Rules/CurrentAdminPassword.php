<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CurrentAdminPassword implements ValidationRule
{
    /**
     * The guard to use for authentication.
     *
     * @var string
     */
    protected $guard;

    /**
     * Create a new rule instance.
     *
     * @param  string  $guard
     * @return void
     */
    public function __construct($guard = 'admin')
    {
        $this->guard = $guard;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = Auth::guard($this->guard)->user();

        if (!$user) {
            $fail('The current password is incorrect.');
            return;
        }

        if (!Hash::check($value, $user->getAuthPassword())) {
            $fail('The current password is incorrect.');
        }
    }
}
