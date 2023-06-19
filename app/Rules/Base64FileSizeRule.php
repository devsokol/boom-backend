<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Base64FileSizeRule implements Rule
{
    private float|int $sizeInKilobytes;

    public function __construct(private int $definedSizeInKilobytes = 0)
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
        $sizeInBytes = (int) (strlen(rtrim($value, '=')) * 3 / 4);

        $this->sizeInKilobytes = $sizeInBytes / 1024;

        if ($this->definedSizeInKilobytes < $this->sizeInKilobytes) {
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
        return __('The :attribute size :size MB is too large', [
            'size' => number_format($this->sizeInKilobytes / 1024, 2, '.', ','),
        ]);
    }
}
