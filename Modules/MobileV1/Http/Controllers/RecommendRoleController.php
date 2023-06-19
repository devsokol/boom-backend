<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Models\Application;
use App\Transformers\RecommendRoleTransformer;
use Illuminate\Http\Response;
use Modules\MobileV1\Actions\RecommendRole\RecommendRoleApply;
use Modules\MobileV1\Actions\RecommendRole\RecommendRoleReject;
use Modules\MobileV1\Actions\RecommendRole\RecommendRoleRelations;
use Modules\MobileV1\Actions\RecommendRole\RecommendRoleRightCheck;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class RecommendRoleController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/recommend-roles/{application_id}/detail",
     *   tags={"RecommendRole"},
     *   summary="Get a recommend role detail",
     *   description="Get a recommend role detail",
     *   operationId="getRecommendRoleDetail",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *     name="application_id",
     *     required=true,
     *     in="path",
     *     description="id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   ),
     * )
     * @codingStandardsIgnoreEnd
     */
    public function recommendRoleDetails(Application $application, RecommendRoleRelations $action): Fractal
    {
        $this->rightsCheck($application);

        abort_unless($application->recommendRole, Response::HTTP_NOT_FOUND);

        $action->handle($application);

        return fractal(
            $application->recommendRole,
            new RecommendRoleTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/recommend-roles/{application_id}/accept",
     *   tags={"RecommendRole"},
     *   summary="Accept a recommend role",
     *   description="Accept a recommend role",
     *   operationId="acceptRecommendRole",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *     name="application_id",
     *     required=true,
     *     in="path",
     *     description="id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   ),
     * )
     * @codingStandardsIgnoreEnd
     */
    public function accept(Application $application, RecommendRoleApply $action): Fractal
    {
        $this->rightsCheck($application);

        abort_unless($application->recommendRole, Response::HTTP_NOT_FOUND);

        $action->handle($application);

        return fractal(
            $application->recommendRole,
            new RecommendRoleTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/recommend-roles/{application_id}/reject",
     *   tags={"RecommendRole"},
     *   summary="Reject a recommend role",
     *   description="Reject a recommend role",
     *   operationId="rejectRecommendRole",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *     name="application_id",
     *     required=true,
     *     in="path",
     *     description="id",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   ),
     * )
     * @codingStandardsIgnoreEnd
     */
    public function reject(Application $application, RecommendRoleReject $action): Fractal
    {
        $this->rightsCheck($application);

        abort_unless($application->recommendRole, Response::HTTP_NOT_FOUND);

        $action->handle($application);

        return fractal(
            $application->recommendRole,
            new RecommendRoleTransformer()
        )->serializeWith(new ArraySerializer());
    }

    private function rightsCheck(Application $application): void
    {
        app(RecommendRoleRightCheck::class)->handle($application);
    }
}
