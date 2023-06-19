<?php

namespace Modules\MobileV1\Pipelines\Application\Conditions;

class EmailCondition
{
    public function __invoke(ApplicationConditionHandler $handler, callable $next): void
    {
        if (empty($handler->getActor()->email)) {
            $handler->setRequirement(__('add email'));
        }

        $next($handler);
    }
}
