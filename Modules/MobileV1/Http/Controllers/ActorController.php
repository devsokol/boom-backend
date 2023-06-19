<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Jobs\ActorSmoothDeleting;
use Illuminate\Http\JsonResponse;
use Modules\MobileV1\Actions\Actor\ActorLogout;

class ActorController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Delete(path="/actor/me",
     *   tags={"Actor"},
     *   summary="Delete an actor",
     *   description="Delete an actor",
     *   operationId="deleteActor",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Response(
     *     response=204,
     *     description="Success",
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated. / This action is unauthorized."
     *   ),
     *   @OA\Response(response=403, description="This action is unauthorized."),
     *   @OA\Response(response=404, description="Not found")
     * )
     * @codingStandardsIgnoreEnd
     */
    public function destroy(ActorLogout $action): JsonResponse
    {
        $actor = auth()->user();

        $actor->markAsDeleted();

        $action->handle($actor);

        ActorSmoothDeleting::dispatch($actor)->delay(now()->addDays(7));

        return response()->json([], 204);
    }
}
