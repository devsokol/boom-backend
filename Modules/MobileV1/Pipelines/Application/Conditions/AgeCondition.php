<?php

namespace Modules\MobileV1\Pipelines\Application\Conditions;

class AgeCondition
{
    public function __invoke(ApplicationConditionHandler $handler, callable $next): void
    {
        $role = $handler->getRole();
        $actorInfo = $handler->getActor()->actorInfo;

        if (! is_null($role->min_age)) {
            if (is_null($actorInfo) || is_null($actorInfo->min_age)) {
                $handler->setRequirement(__('to define min age in your profile'));
            } else if ($role->min_age > $actorInfo?->min_age) {
                $handler->setRequirement(__('the minimum age of the actor is not suitable for this role'));
            }
        }

        if (! is_null($role->max_age)) {
            if (is_null($actorInfo) || is_null($actorInfo->max_age)) {
                $handler->setRequirement(__('to define max age in your profile'));
            } else if ($role->max_age < $actorInfo?->max_age) {
                $handler->setRequirement(__('the maximum age of the actor is not suitable for this role'));
            }
        }

        $next($handler);
    }
}
