<?php

namespace Modules\MobileV1\Services\Actor;

use App\Models\Actor;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    public function update(array $params): Actor
    {
        $params = collect($params);

        return DB::transaction(function () use ($params) {
            $actor = auth()->user();

            $actor->update($params->only('first_name', 'last_name')->toArray());

            $actorInfo = $params->except(
                'first_name',
                'last_name',
                'skill_list',
                'headshots',
                'selftapes'
            )->toArray();

            $actor->actorInfo()->updateOrCreate(['actor_id' => $actor->getKey()], $actorInfo);

            $actor->personalSkills()->sync($params->only('skill_list')->first());

            // $this->storeHeadshots($actor, (array) $params->only('headshots')->first());
            // $this->storeSelftapes($actor, (array) $params->only('selftapes')->first());

            return $actor;
        });
    }

    /*
     * TODO
     * delete.
     */
    // public function storeHeadshots(Model|Authenticatable|null $actor, array $headshots): iterable
    // {
    //     if ((isset($headshots) && ! empty($headshots)) && $actor) {
    //         $headshotModelList = array_map(function ($headshot) {
    //             return new Headshot(['headshot' => $headshot]);
    //         }, $headshots);

    //         return $actor->headshots()->saveMany($headshotModelList);
    //     }

    //     return [];
    // }

    /*
     * TODO
     * delete.
     */
    // public function storeSelftapes(Model|Authenticatable|null $actor, array $selftapes): iterable
    // {
    //     if (! empty($selftapes) && $actor) {
    //         $selftapeModelList = array_map(function ($video) {
    //             return new Selftape(['video' => $video]);
    //         }, $selftapes);

    //         return $actor->selftapes()->saveMany($selftapeModelList);
    //     }

    //     return [];
    // }
}
