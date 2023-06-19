<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class Base64ImageRule implements Rule
{
    private array $allowFormats = [];

    /**
     * Create a new rule instance.
     *
     * @param  array  $allowFormats
     */
    public function __construct(array $allowFormats = ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'heic', 'webp'])
    {
        $this->allowFormats = $allowFormats;
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

        [$type, $data] = explode(';', $value);

        $format = str_replace(['data:image/', ';', 'base64'], ['', '', ''], $type);

        $base64 = Str::after($data, 'base64,');

        if (! in_array($format, $this->allowFormats)) {
            return false;
        }

        if (! (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $base64)) {
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
        return __('The :attribute is invalid.');
    }
}
