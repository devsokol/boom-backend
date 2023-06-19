<?php

namespace App\Rules;

use App\Exceptions\TraitNotFoundException;
use App\Traits\Model\HasVerificationCode;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class ExistsUserByGatewayRule implements Rule
{
    private string $searchField;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        private string $modelClass,
        private string $gatewayFieldnameInRequest = 'gateway'
    )
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
        $user = app($this->modelClass);

        if (! isExistsTraitInClass(HasVerificationCode::class, $user)) {
            throw new TraitNotFoundException(sprintf(
                'Trait [%s] is not found in model: %s',
                HasVerificationCode::class,
                get_class($user)
            ));
        }

        $gateway = request()->get($this->gatewayFieldnameInRequest);

        $this->searchField = $user->getSearchFieldByGateway($gateway);

        return $user->where($this->searchField, $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        /* return __('There is no user with such an :regular', [
            'regular' => Str::of($this->searchField)->replace('_', ' '),
        ]); */

        return __('We have now sent a password reset link to you if the email address was registered.');
    }
}
