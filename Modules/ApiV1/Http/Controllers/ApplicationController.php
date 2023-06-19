<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\Role;
use App\Models\Application;
use Spatie\Fractal\Fractal;
use Illuminate\Http\Request;
use Spatie\Fractalistic\ArraySerializer;
use League\Fractal\Serializer\JsonApiSerializer;
use Modules\ApiV1\Actions\Application\ApplicationList;
use Modules\ApiV1\Transformers\ApplicationTransformer;
use Modules\ApiV1\Actions\Application\ApplicationAccept;
use Modules\ApiV1\Actions\Application\ApplicationReject;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Modules\ApiV1\Actions\Application\ApplicationRelations;
use Modules\ApiV1\Actions\Application\ApplicationStatusesList;
use Modules\ApiV1\Actions\Application\ApplicationListCheckRights;
use Modules\ApiV1\Actions\Role\RoleUpdateAmountOfViewedApplications;
use Modules\ApiV1\Actions\Application\ApplicationCheckingWhetherRequestIsAllowed;

class ApplicationController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/applications/statuses",
     *   tags={"Application"},
     *   summary="Fetch application statuses",
     *   description="Fetch application statuses",
     *   operationId="getApplicationStatuses",
     *   security={ {"bearerAuth": {} }},
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
     * )
     * @codingStandardsIgnoreEnd
     */
    public function statuses(ApplicationStatusesList $action): array
    {
        return Application::makeCacheByUniqueRequest(function () use ($action) {
            return $action->handle();
        });
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/roles/{id}/applications",
     *   tags={"Application"},
     *   summary="Fetch applications",
     *   description="Fetch applications where a role has an active project",
     *   operationId="fetchApplications",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="id",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *   @OA\Parameter(
     *       name="filterByStatuses[]",
     *       in="query",
     *       required=false,
     *       description="array of ids",
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(type="integer"),
     *       ),
     *       style="form"
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
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function index(Role $role, Request $request): Fractal
    {
        (new ApplicationListCheckRights())->handle($role);

        $paginator = (new ApplicationList())->handle($role, $request->all());

        $applications = $paginator->getCollection();

        (new RoleUpdateAmountOfViewedApplications())->handle($role);

        return fractal($applications, new ApplicationTransformer())
            ->withResourceName('application')
            ->serializeWith(new JsonApiSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator));
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/roles/{role_id}/applications/{application_id}",
     *   tags={"Application"},
     *   summary="Fetch an application",
     *   description="Fetch an application by id",
     *   operationId="fetchApplication",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="role_id",
     *       required=true,
     *       in="path",
     *       description="Role ID",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
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
    public function show(Role $role, Application $application): Fractal
    {
        (new ApplicationListCheckRights())->handle($role);
        (new ApplicationRelations())->handle($application);

        return fractal($application, new ApplicationTransformer(true))->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/applications/{application_id}/accept",
     *   tags={"Application"},
     *   summary="Give a role for a user",
     *   description="Give a role for a user by an application_id",
     *   operationId="acceptRoleForActor",
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
    public function acceptApplication(Application $application): Fractal
    {
        (new ApplicationCheckingWhetherRequestIsAllowed())->handle($application);
        (new ApplicationListCheckRights())->handle($application);
        (new ApplicationAccept())->handle($application);

        return fractal($application, new ApplicationTransformer(true))->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/applications/{application_id}/reject",
     *   tags={"Application"},
     *   summary="Reject a role for a user",
     *   description="Reject a role for a user by an application_id",
     *   operationId="rejectRoleForActor",
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
    public function rejectApplication(Application $application): Fractal
    {
        (new ApplicationCheckingWhetherRequestIsAllowed())->handle($application);
        (new ApplicationListCheckRights())->handle($application);
        (new ApplicationReject())->handle($application);

        return fractal($application, new ApplicationTransformer(true))->serializeWith(new ArraySerializer());
    }
}
