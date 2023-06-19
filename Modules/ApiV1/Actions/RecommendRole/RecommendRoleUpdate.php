<?php

namespace Modules\ApiV1\Actions\RecommendRole;

use App\Enums\RecommendRoleStatus;
use App\Models\RecommendRole;
use Illuminate\Http\Response;

class RecommendRoleUpdate
{
    public function handle(int $roleId, RecommendRole $recommendRole): bool
    {
        if ($recommendRole->status !== RecommendRoleStatus::IN_REVIEW) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('It is not possible to change '
                . 'the role once the decision has been made'));
        }

        return $recommendRole->update([
            'role_id' => $roleId,
        ]);
    }
}
