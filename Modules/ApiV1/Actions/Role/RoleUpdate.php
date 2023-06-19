<?php

namespace Modules\ApiV1\Actions\Role;

use App\Models\Role;
use App\Dto\RoleData;
use Illuminate\Support\Facades\DB;
use Modules\ApiV1\Actions\Role\GenerateDynamicLinkForRole;

class RoleUpdate
{
    public function handle(RoleData $roleData, Role $role): Role
    {
        return DB::transaction(function () use ($roleData, $role) {
            $data = $roleData->except('personal_skills', 'materials')->toArray();

            $role->update($data);

            (new GenerateDynamicLinkForRole)->handle($role, updateModel: true);

            $role->personalSkills()->sync($roleData->personal_skills);

            $pickShootingDates = $roleData->pick_shooting_dates ?? null;

            $role->pickShootingDates()->delete();

            if ($pickShootingDates) {
                $role->pickShootingDates()->createMany($pickShootingDates);
            }

            $materials = $roleData->materials ?? null;

            if ($materials) {
                $role->roleMaterials()->createMany($materials);
            }

            return $role;
        });
    }
}
