<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class HexColorRule implements Rule
{
    /**
     * Hex color regex formats.
     */
    private array $formats = [
        4 => '/^#([a-fA-F0-9]{3})$/i',
        7 => '/^#([a-fA-F0-9]{6})$/i',
        9 => '/^#([a-fA-F0-9]{6}[0-9]{2})$/i',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! is_string($value) || ! in_array(strlen($value), array_keys($this->formats))) {
            return false;
        }

        $match = preg_match($this->formats[strlen($value)], $value);

        return $match > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The :attribute has to be a valid hex color.');
    }
}
