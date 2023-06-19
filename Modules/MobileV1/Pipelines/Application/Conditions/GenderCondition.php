<?php

namespace Modules\MobileV1\Pipelines\Application\Conditions;

class GenderCondition
{
    public function __invoke(ApplicationConditionHandler $handler, callable $next): void
    {
        if (is_null($handler->getActor()->actorInfo)) {
            $handler->setRequirement(__('create portfolio'));
        }

        $roleGenders = (array) $handler->getRole()?->acting_gender;
        $actorGenders = (array) $handler->getActor()?->actorInfo?->acting_gender;

        if (! empty($roleGenders) && empty($actorGenders)) {
            $handler->setRequirement(__('to define gender in your profile'));
        }

        if (! empty($roleGenders) && ! array_intersect($roleGenders, $actorGenders)) {
            $handler->throwError(__('You can\'t claim for the role because '
                . 'your gender does not meet requirements'));
        }

        $next($handler);
    }
}
