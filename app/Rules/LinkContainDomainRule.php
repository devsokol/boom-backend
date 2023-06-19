<?php

namespace App\Rules;

use App\Exceptions\EmptyParameterException;
use Illuminate\Contracts\Validation\Rule;

class LinkContainDomainRule implements Rule
{
    public function __construct(private array $domains = [])
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
        $pattern = $this->preparePatternWithDomains();

        return (bool) preg_match($pattern, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid link';
    }

    private function preparePatternWithDomains(): string
    {
        if (empty($this->domains)) {
            throw new EmptyParameterException('Parameter [domains] cannot be empty');
        }

        $domainsAsString = implode('|', $this->domains);

        $domainsAsString = str_replace('.', '\.', $domainsAsString);

        return "/^http[s]?:\/\/(?:www\.)?(?:{$domainsAsString})\/.+$/";
    }
}
