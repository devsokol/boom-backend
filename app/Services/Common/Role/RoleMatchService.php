<?php

namespace App\Services\Common\Role;

use App\Models\Actor;
use App\Models\ActorInfo;
use App\Models\Role;
use Exception;

class RoleMatchService
{
    private Actor $actor;

    public function __construct(private ?Role $role, ?Actor $actor = null)
    {
        if ($actor) {
            $this->actor = $actor;
        } else {
            $this->actor = auth()->user();
        }
    }

    public function isMatch(): bool
    {
        if (! $this->role) {
            return false;
        }

        return $this->isMatchByActingGender()
            && $this->isMatchMinMaxAge()
            && $this->isMatchEthnicity()
            && $this->isMatchSkills();
    }

    public function matches(): array
    {
        return [
            'acting_gender' => $this->isMatchByActingGender(),
            'acting_age_range' => $this->isMatchMinMaxAge(),
            'ethnicity' => $this->isMatchEthnicity(),
            'personal_skills' => $this->matchSkills(),
        ];
    }

    private function isMatchByActingGender(): bool
    {
        if (is_bool($this->checkActingGenderProperties())) {
            return $this->checkActingGenderProperties();
        }

        try {
            foreach ($this->role->acting_gender as $gender) {
                if (in_array($gender, (array) $this->actor->actorInfo->acting_gender)) {
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    private function isMatchMinMaxAge(): bool
    {
        $actorMinAge = $this->actor?->actorInfo?->min_age;
        $actorMaxAge = $this->actor?->actorInfo?->max_age;

        $roleMinAge = $this->role->min_age;
        $roleMaxAge = $this->role->max_age;

        if (is_bool($this->checkMinMaxAgeProperties())) {
            return $this->checkMinMaxAgeProperties();
        }

        try {
            if (($actorMinAge && ! $actorMaxAge) or (! $actorMinAge && $actorMaxAge)) {
                $age = $actorMinAge ?? $actorMaxAge;

                return $roleMinAge <= $age && $roleMaxAge >= $age;
            } else {
                $rangeActorAges = generateSequenceNumbers($actorMinAge, $actorMaxAge);
                $rangeRoleAges = generateSequenceNumbers($roleMinAge, $roleMaxAge);

                if (count(array_intersect($rangeActorAges, $rangeRoleAges)) > 0) {
                    return true;
                }
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    private function isMatchEthnicity(): bool
    {
        if (is_bool($this->checkEthnicityProperties())) {
            return $this->checkEthnicityProperties();
        }

        try {
            return $this->role->ethnicity_id === $this->actor->actorInfo->ethnicity_id;
        } catch (Exception $e) {
            return false;
        }
    }

    private function isMatchSkills(): bool
    {
        if (is_bool($this->checkPersonalSkillProperties())) {
            return $this->checkPersonalSkillProperties();
        }

        try {
            $numberOfMatches = 0;

            $rolePersonalSkills = $this->role->personalSkills->pluck('id')->toArray();
            $actorPersonalSkills = $this->actor->personalSkills->pluck('id')->toArray();

            foreach ($rolePersonalSkills as $rolePersonalSkill) {
                if (! in_array($rolePersonalSkill, $actorPersonalSkills)) {
                    $numberOfMatches++;
                }
            }

            return $numberOfMatches >= count($rolePersonalSkill);
        } catch (Exception) {
            return false;
        }
    }

    private function matchSkills(): array
    {
        $matches = [];

        $rolePersonalSkills = $this->role->personalSkills?->pluck('id')?->toArray();
        $actorPersonalSkills = $this->actor->personalSkills?->pluck('id')?->toArray();

        if (is_array($rolePersonalSkills) && is_array($actorPersonalSkills)) {
            foreach ($rolePersonalSkills as $rolePersonalSkill) {
                if (in_array($rolePersonalSkill, $actorPersonalSkills)) {
                    $matches[] = $rolePersonalSkill;
                }
            }
        }

        return $matches;
    }

    private function checkActingGenderProperties(): ?bool
    {
        return $this->checkProperty('acting_gender', $this->actor->actorInfo);
    }

    private function checkMinMaxAgeProperties(): ?bool
    {
        if (is_bool($this->checkProperty('min_age', $this->actor->actorInfo))) {
            return $this->checkProperty('min_age', $this->actor->actorInfo);
        }

        if (is_bool($this->checkProperty('max_age', $this->actor->actorInfo))) {
            return $this->checkProperty('max_age', $this->actor->actorInfo);
        }

        return null;
    }

    private function checkEthnicityProperties(): ?bool
    {
        return $this->checkProperty('ethnicity_id', $this->actor->actorInfo);
    }

    private function checkPersonalSkillProperties(): ?bool
    {
        return $this->checkProperty('personalSkills', $this->actor);
    }

    private function checkProperty(string $param, Actor|ActorInfo|null $comparisonModel): ?bool
    {
        if (is_null($this->role->{$param})) {
            return true;
        }

        if (is_null($this->role->{$param}) && is_null($comparisonModel?->{$param})) {
            return true;
        }

        if (is_null($comparisonModel?->{$param})) {
            return false;
        }

        return null;
    }
}
