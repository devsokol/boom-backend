<?php

namespace Modules\ApiV1\Actions\Role;

use App\Models\Role;

class RoleUpdateLabelColorRange
{
    public function handle(Role $role, ?string $darkColor, ?string $lightColor): bool
    {
        return $role->update(['label_range_color' => [$darkColor, $lightColor]]);
    }
}
