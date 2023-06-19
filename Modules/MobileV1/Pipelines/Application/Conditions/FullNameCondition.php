<?php

namespace Modules\MobileV1\Pipelines\Application\Conditions;

class FullNameCondition
{
    public function __invoke(ApplicationConditionHandler $handler, callable $next): void
    {
        if (empty($handler->getActor()->first_name) || empty($handler->getActor()->last_name)) {
            $handler->setRequirement(__('add full name'));
        }

        $next($handler);
    }
}
