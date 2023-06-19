<?php

namespace App\Rules;

use App\Models\Application;
use Illuminate\Contracts\Validation\Rule;

class PreventIdenticalRecommendRoleRule implements Rule
{
    public function __construct(private ?Application $application)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! $this->application) {
            return false;
        }

        return (int) $value !== $this->application->role_id;
    }

    public function message(): string
    {
        return __('This actor already applied for this. Please select different role from the list.');
    }
}
