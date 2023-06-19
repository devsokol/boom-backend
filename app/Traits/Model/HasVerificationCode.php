<?php

namespace App\Traits\Model;

use App\Exceptions\UnsupportedParameterException;
use App\Models\VerificationCode;
use App\Services\Sms\Jobs\SendSmsJob;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Modules\MobileV1\Emails\SendVerificationCodeMail;

trait HasVerificationCode
{
    public string $verificationTag = 'verify';

    //region mutators
    public function codes(): MorphMany
    {
        return $this->morphMany(VerificationCode::class, 'codeable');
    }
    //endregion mutators

    public static function verificationCodeLifetimeForHuman(): string
    {
        $seconds = (int) config('code_verify.verification_code_lifetime');

        $minutes = now()->diffInMinutes(now()->addSeconds($seconds));

        return __choice('{1}:value min ute|[2,*]:value minutes', $minutes, [
            'value' => $minutes,
        ]);
    }

    public function isVerifyCodeValid(string|int $code): bool
    {
        return $this
            ->codes()
            ->toBase()
            ->select('code')
            ->where([['code', $code], ['tag', $this->verificationTag], ['expires_at', '>', now()]])
            ->exists();
    }

    public function isCodeOwner(string|int $code, string $compareField): bool
    {
        $user = VerificationCode::getVerificationModelByCode($code);

        if (! $user) {
            return false;
        }

        return $user->{$compareField} === $this->{$compareField};
    }

    public function hasVerifiedStatus(): bool
    {
        $fieldName = $this->verificationStatusFieldName();

        return (bool) $this->{$fieldName};
    }

    public function createVerificationCode(int $length = 4, DateTimeInterface $expiresAt = null): mixed
    {
        $code = $this->codeGenerator($length);

        $seconds = (int) config('code_verify.verification_code_lifetime');

        $this->clearVerificationCodes();

        $this->codes()->create([
            'code' => $code,
            'tag' => $this->verificationTag,
            'expires_at' => $expiresAt ?? now()->addSeconds($seconds),
        ]);

        return $code;
    }

    public function isUserCanSendNewVerificationCode(): bool
    {
        $delayInSeconds = (int) config('code_verify.verification_code_repeat_delay');

        $res = $this
            ->codes()
            ->toBase()
            ->select('created_at')
            ->where('tag', $this->verificationTag)
            ->latest()
            ->first();

        if (! $res) {
            return true;
        }

        return now()->diffInSeconds($res->created_at) > intval($delayInSeconds);
    }

    public function clearVerificationCodes(): void
    {
        $this->codes()->where('tag', $this->verificationTag)->delete();
    }

    public function verificationStatusFieldName(): string
    {
        return 'is_account_verified';
    }

    public function emailFieldName(): string
    {
        return 'email';
    }

    public function phoneNumberFieldName(): string
    {
        return 'phone_number';
    }

    public function verificationViaGateway(): string
    {
        return (string) config('code_verify.verification_via_gateway');
    }

    private function codeGenerator(int $length = 4): mixed
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;

        $code = rand($min, $max);

        return $this->checkUniqueKeyForUser($code, $length);
    }

    private function checkUniqueKeyForUser(string|int $code, int $length): string|int
    {
        $codeAlreadyExists = $this->codes()->toBase()->where('code', $code)->exists();

        if ($codeAlreadyExists) {
            $newCode = $this->codeGenerator($length);

            return $this->checkUniqueKeyForUser($newCode, $length);
        }

        return $code;
    }

    private string|int $verificationCode;

    public function sendVerificationCode(?string $tag = null, int $length = 4): void
    {
        if (! empty($tag)) {
            $this->verificationTag = $tag;
        }

        $this->checkIsUserCanSendNewVerificationCode();

        $this->verificationCode = $this->createVerificationCode(length: $length);

        $verificationViaGateway = $this->getVerificationGateway();

        match ($verificationViaGateway) {
            'sms' => $this->sendViaSms(),
            'email' =>  $this->sendViaEmail(),
            default => throw new UnsupportedParameterException(
                sprintf('Unsupported parameter: [%s]', $verificationViaGateway)
            ),
        };
    }

    public function afterSuccessfulVerification(callable $callback, ?string $tag = null): mixed
    {
        if (! empty($tag)) {
            $this->verificationTag = $tag;
        }

        $result = $callback($this);

        if ($result) {
            $this->clearVerificationCodes();
        }

        return $this;
    }

    public function getSearchFieldByGateway(string $gateway): string
    {
        return match ($gateway) {
            'sms' => $this->phoneNumberFieldName(),
            'email' =>  $this->emailFieldName(),
            default => throw new UnsupportedParameterException("Gateway [{$gateway}] is not implemented."),
        };
    }

    private function sendViaSms(): void
    {
        $phoneNumber = $this->extractFieldValueFromModel('phoneNumberFieldName', 'phone_number');

        $smsTemplate = __('Verification code: :value', ['value' => $this->verificationCode]);

        SendSmsJob::dispatch($phoneNumber, $smsTemplate);
    }

    private function sendViaEmail(): void
    {
        $email = $this->extractFieldValueFromModel('emailFieldName', 'email');

        $verificationCodeLifetime = static::verificationCodeLifetimeForHuman();

        Mail::to($email)->send(new SendVerificationCodeMail($this->verificationCode, $verificationCodeLifetime));
    }

    private function extractFieldValueFromModel(string $methodResolver, string $field = 'email'): string
    {
        $value = $this->{$field};

        if (method_exists($this, $methodResolver)) {
            $fieldName = $this->{$methodResolver}();

            $value = $this->{$fieldName};
        }

        return $value;
    }

    private function getVerificationGateway(): string
    {
        $gateway = (string) config('code_verify.verification_via_gateway');

        if (method_exists($this, 'verificationViaGateway')) {
            $gateway = $this->verificationViaGateway();
        }

        return $gateway;
    }

    private function checkIsUserCanSendNewVerificationCode(): void
    {
        $isCanReset = $this->isUserCanSendNewVerificationCode();

        if (! $isCanReset) {
            abort(Response::HTTP_TOO_MANY_REQUESTS, __('Forbidden frequently send verification code requests.'));
        }
    }
}
