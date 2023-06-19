<?php

namespace Modules\MobileV1\Actions\Role;

use App\Models\Role;
use App\Models\Actor;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class RoleList
{
    public function handle(array $filters): LengthAwarePaginator
    {
        /** @var Actor $actor */
        $actor = auth()->user();
        $actor->load('actorInfo');

        return Role::query()
            ->select(DB::raw('*'))
            ->selectSub($this->matchRoleLabelSubQuery($actor), 'is_match')
            ->with([
                'currency',
                'paymentType',
                'personalSkills',
                'project.user',
                'project.genre',
                'project.projectType',
            ])
            ->withExists(['actorBookmarks' => fn ($q) => $q->whereActorId($actor->getKey())])
            ->public()
            ->onlyActiveProject()
            ->filter($filters)
            ->orderBy('is_match', 'DESC')
            ->latest()
            ->jsonPaginate();
    }

    private function matchRoleLabelSubQuery(Actor $actor):
        \Illuminate\Database\Eloquent\Builder | \Illuminate\Database\Query\Builder
    {
        /** @var array $actorGenders */
        $actorGenders = $actor->actorInfo?->acting_gender ?? [];
        $actorMinAge = $actor->actorInfo?->min_age;
        $actorMaxAge = $actor->actorInfo?->max_age;
        $actorEthnicity = $actor->actorInfo?->ethnicity_id;
        $actorPersonalSkills = $actor->personalSkills()->pluck('id')->toArray();

        return Role::query()
            ->from(DB::raw('roles as r'))
            ->select(DB::raw('COUNT(r.id) as amount'))
            ->where(function (Builder $q) use ($actorPersonalSkills) {
                $q->whereRaw('(SELECT COUNT(role_id) FROM role_personal_skill WHERE role_id = r.id) = 0');
                $q->when((bool) count($actorPersonalSkills), function ($q) use ($actorPersonalSkills) {
                    $additionalConditions = array_fill(0, count($actorPersonalSkills), 'rps.personal_skill_id = ?');

                    $expression = '(SELECT COUNT(*) FROM role_personal_skill rps WHERE rps.role_id = r.id AND (%s))';
                    $expression .= ' >= ';
                    $expression .= '(SELECT COUNT(role_id) FROM role_personal_skill WHERE role_id = r.id)';

                    $rawQuery = sprintf($expression, implode(' OR ', $additionalConditions));

                    $q->whereRaw($rawQuery, $actorPersonalSkills, 'or');
                });
            })
            ->where(function (Builder $q) use ($actorGenders) {
                $q->where('acting_gender', '');
                $q->when(count($actorGenders) > 0, function (Builder $q) use ($actorGenders) {
                    $q->whereIn('acting_gender', $actorGenders, 'or');
                });
            })
            ->where(function (Builder $q) use ($actorMinAge, $actorMaxAge) {
                if (($actorMinAge && ! $actorMaxAge) or (! $actorMinAge && $actorMaxAge)) {
                    $expression = "(min_age <= ? and max_age>=?)";

                    $age = $actorMinAge ?? $actorMaxAge;

                    $q->whereRaw($expression, [$age, $age]);
                } else {
                    $ageParams = [
                        $actorMinAge,
                        $actorMaxAge,
                    ];

                    $expression = "((min_age >= ? and min_age<=?)";
                    $expression .= " or ";
                    $expression .= "(max_age>=? and max_age<=?))";
                    $expression .= " or ";
                    $expression .= "((?>=min_age and ?<=max_age))";

                    $q->whereRaw($expression, [
                        ...$ageParams, ...$ageParams, ...$ageParams,
                    ]);
                }
            })
            ->where(function (Builder $q) use ($actorEthnicity) {
                $q->where('ethnicity_id', null);
                $q->when($actorEthnicity, function (Builder $q) use ($actorEthnicity) {
                    $q->orWhere('ethnicity_id', $actorEthnicity);
                });
            })
            ->where('id', DB::raw('roles.id'));
    }
}
