<?php

namespace Modules\MobileV1\Pipelines\Application\Conditions;

class PhoneNumberCondition
{
    public function __invoke(ApplicationConditionHandler $handler, callable $next): void
    {
        if (empty($handler->getActor()->phone_number)) {
            $handler->setRequirement(__('add phone number'));
        }

        $next($handler);
    }
}
