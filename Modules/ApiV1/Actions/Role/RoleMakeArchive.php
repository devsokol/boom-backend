<?php

namespace Modules\ApiV1\Actions\Role;

use App\Enums\RoleStatus;
use App\Models\Role;

class RoleMakeArchive
{
    public function handle(Role $role): bool
    {
        return $role->update(['status' => RoleStatus::ARCHIVE->value]);
    }
}
