<?php

namespace Modules\MobileV1\Pipelines\Application\Conditions;

class SkillCondition
{
    public function __invoke(ApplicationConditionHandler $handler, callable $next): void
    {
        $role = $handler->getRole();

        if ($role->personalSkills()->exists()) {
            if (! $handler
                ->getActor()
                ->personalSkills()
                ->exists()
            ) {
                $handler->setRequirement(__('to define personal skills in your profile'));
            } else {
                $roleSkills = $role->personalSkills
                    ->pluck('id')
                    ->toArray();

                $actorSkills = $handler->getActor()->personalSkills
                    ->pluck('id')
                    ->toArray();

                if (! array_intersect($roleSkills, $actorSkills)) {
                    $handler->setRequirement(__('the personal skills of the actor is not suitable for this role'));
                }
            }
        }

        $next($handler);
    }
}
