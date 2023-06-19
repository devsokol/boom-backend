<?php

namespace Modules\MobileV1\Pipelines\Application\Conditions;

class EthnicityCondition
{
    public function __invoke(ApplicationConditionHandler $handler, callable $next): void
    {
        $role = $handler->getRole();
        $actorInfo = $handler->getActor()->actorInfo;

        if (! is_null($role->ethnicity_id)) {
            if (is_null($actorInfo?->ethnicity_id)) {
                $handler->setRequirement(__('to define ethnicity in your profile'));
            } else if ($role->ethnicity_id !== $actorInfo->ethnicity_id) {
                $handler->setRequirement(__('the ethnicity of the actor is not suitable for this role'));
            }
        }

        $next($handler);
    }
}
