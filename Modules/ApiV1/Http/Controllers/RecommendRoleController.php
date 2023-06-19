<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\Application;
use App\Transformers\RecommendRoleTransformer;
use Illuminate\Http\Response;
use Modules\ApiV1\Actions\Application\ApplicationCheckingWhetherRequestIsAllowed;
use Modules\ApiV1\Actions\RecommendRole\RecommendRoleCheckRights;
use Modules\ApiV1\Actions\RecommendRole\RecommendRoleRelations;
use Modules\ApiV1\Actions\RecommendRole\RecommendRoleStore;
use Modules\ApiV1\Actions\RecommendRole\RecommendRoleUpdate;
use Modules\ApiV1\Http\Requests\RecommendRoleRequest;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class RecommendRoleController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/applications/{application_id}/recommend-role",
     *   tags={"RecommendRole"},
     *   summary="Get a recommend role request",
     *   description="Get a recommend a role request",
     *   operationId="getRecommendRoleRequest",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="application_id",
     *       required=true,
     *       in="path",
     *       description="Application ID",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *       content={@OA\MediaType(mediaType="application/json",),}
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
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function show(Application $application): Fractal
    {
        (new RecommendRoleCheckRights())->handle($application);

        abort_unless($application->recommendRole, Response::HTTP_NOT_FOUND);

        (new RecommendRoleRelations())->handle($application);

        return fractal(
            $application->recommendRole,
            new RecommendRoleTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/applications/{application_id}/recommend-role",
     *   tags={"RecommendRole"},
     *   summary="A recommend role request",
     *   description="A recommend a role request",
     *   operationId="createRecommendRoleRequest",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="application_id",
     *       required=true,
     *       in="path",
     *       description="Application ID",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={
     *                   "role_id"
     *               },
     *               @OA\Property(
     *                   property="role_id",
     *                   description="Role ID",
     *                   type="integer",
     *                   example="1"
     *               )
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *       content={@OA\MediaType(mediaType="application/json",),}
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
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function store(RecommendRoleRequest $request, Application $application): Fractal
    {
        (new ApplicationCheckingWhetherRequestIsAllowed())->handle($application);

        $roleId = (int) $request->role_id;

        (new RecommendRoleCheckRights())->handle($application, $roleId);

        $recommendRole = (new RecommendRoleStore())->handle($roleId, $application);

        $recommendRole->load('role');

        return fractal($recommendRole, new RecommendRoleTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Put(path="/applications/{application_id}/recommend-role",
     *   tags={"RecommendRole"},
     *   summary="Change a recommend role",
     *   description="Change a recommend role",
     *   operationId="changeRecommendRole",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="application_id",
     *       required=true,
     *       in="path",
     *       description="Application ID",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={
     *                   "role_id"
     *               },
     *               @OA\Property(
     *                   property="role_id",
     *                   description="Role ID",
     *                   type="integer",
     *                   example="1"
     *               )
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *       content={@OA\MediaType(mediaType="application/json",),}
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
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function update(RecommendRoleRequest $request, Application $application): Fractal
    {
        (new RecommendRoleCheckRights())->handle($application);
        (new RecommendRoleUpdate())->handle($request->role_id, $application->recommendRole);
        (new RecommendRoleRelations())->handle($application);

        return fractal(
            $application->recommendRole,
            new RecommendRoleTransformer()
        )->serializeWith(new ArraySerializer());
    }
}
