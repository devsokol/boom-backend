<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class Base64ImageMaxDimensionRule implements Rule
{
    private int $width;

    private int $height;

    /**
     * Create a new rule instance.
     *
     * @param  int  $width
     * @param  int  $height
     */
    public function __construct(int $width, int $height = 0)
    {
        $this->width = $width;
        $this->height = $height;
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
        if (! Str::startsWith($value, 'data:image')) {
            return false;
        }

        $size = getimagesize($value);

        if (! $size) {
            return false;
        }

        if ($this->width && $this->width < $size[0]) {
            return false;
        }

        if ($this->height && $this->height < $size[1]) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The :attribute dimension (in px) too large.');
    }
}
