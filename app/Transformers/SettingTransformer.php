<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class SettingTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(): array
    {
        return [
            'allowed_resend_verification_code_via_seconds' => config('app.allowed_resend_verification_code_via_seconds'),
        ];
    }
}
