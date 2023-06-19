<?php

namespace App\Rules;

use App\Utils\FileUtility;
use Illuminate\Contracts\Validation\Rule;

class Base64MimesRule implements Rule
{
    private FileUtility $fileUtility;

    public function __construct(
        private array $allowFormats = []
    ) {
        $this->fileUtility = app(FileUtility::class);
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
        $this->fileUtility->isFormatsAvailableInSignatures($this->allowFormats);

        return $this->fileUtility->isDetectMimeTypeByAllowFormats($value, $this->allowFormats);
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
