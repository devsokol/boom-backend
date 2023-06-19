<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Models\Application;
use App\Models\Role;
use App\Rules\PreventXssRule;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\JsonApiSerializer;
use Modules\MobileV1\Actions\Application\ApplicationAcceptApproval;
use Modules\MobileV1\Actions\Application\ApplicationApplyForRole;
use Modules\MobileV1\Actions\Application\ApplicationCheckIsAlreadyApplied;
use Modules\MobileV1\Actions\Application\ApplicationCheckRights;
use Modules\MobileV1\Actions\Application\ApplicationList;
use Modules\MobileV1\Actions\Application\ApplicationRejectApproval;
use Modules\MobileV1\Actions\Application\ApplicationRelations;
use Modules\MobileV1\Pipelines\Application\Conditions\ApplicationConditionHandler;
use Modules\MobileV1\Pipelines\Application\Conditions\EmailCondition;
use Modules\MobileV1\Pipelines\Application\Conditions\FullNameCondition;
use Modules\MobileV1\Pipelines\Application\Conditions\GenderCondition;
use Modules\MobileV1\Pipelines\Application\Conditions\HeadshotCondition;
use Modules\MobileV1\Pipelines\Application\Conditions\PhoneNumberCondition;
use Modules\MobileV1\Pipelines\Application\Conditions\SelftapeCondition;
use Modules\MobileV1\Pipelines\Application\Conditions\AgeCondition;
use Modules\MobileV1\Pipelines\Application\Conditions\EthnicityCondition;
use Modules\MobileV1\Pipelines\Application\Conditions\SkillCondition;
use Modules\MobileV1\Transformers\ApplicationTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class ApplicationController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/applications/role/{id}/apply",
     *   tags={"Application"},
     *   summary="Apply for a role",
     *   description="Apply for a role",
     *   operationId="applicationApplyForRole",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="Role ID",
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
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   ),
     *   @OA\Response(
     *       response=424,
     *       description="Failed Dependency"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function applyForRole(Role $role): Fractal
    {
        (new ApplicationCheckIsAlreadyApplied())->handle($role);

        app(Pipeline::class)
            ->send(new ApplicationConditionHandler($role))
            ->through([
                FullNameCondition::class,
                PhoneNumberCondition::class,
                EmailCondition::class,
                HeadshotCondition::class,
                SelftapeCondition::class,
                GenderCondition::class,
            ])
            ->then(function ($handler) {
                $handler->abortIfExistsRequirements();
            });

        $application = (new ApplicationApplyForRole())->handle($role);

        (new ApplicationRelations())->handle($application);

        return fractal($application, new ApplicationTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/applications",
     *   tags={"Application"},
     *   summary="Fetch own applications",
     *   description="Fetch own applications",
     *   operationId="fetchApplications",
     *   security={ {"bearerAuth": {} }},
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
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function index(ApplicationList $action): Fractal
    {
        $paginator = $action->handle();

        $applications = $paginator->getCollection();

        return fractal($applications, new ApplicationTransformer())
                ->withResourceName('application')
                ->serializeWith(new JsonApiSerializer())
                ->paginateWith(new IlluminatePaginatorAdapter($paginator));
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/applications/{application_id}/show",
     *   tags={"Application"},
     *   summary="Fetch an application",
     *   description="Fetch an application",
     *   operationId="fetchApplication",
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
    public function show(Application $application): Fractal
    {
        // (new ApplicationCheckRights())->handle($application);
        (new ApplicationRelations())->handle($application);

        return fractal($application, new ApplicationTransformer(true))->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/applications/{application_id}/accept-approval",
     *   tags={"Application"},
     *   summary="Accept the offer for the main role",
     *   description="Accept the offer for the main role by an application_id",
     *   operationId="acceptOfferForMainRole",
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
    public function acceptApproval(Application $application): Fractal
    {
        (new ApplicationCheckRights())->handle($application);
        (new ApplicationAcceptApproval())->handle($application);

        return fractal($application, new ApplicationTransformer(true))->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/applications/{application_id}/reject-approval",
     *   tags={"Application"},
     *   summary="Decline the offer for the main role",
     *   description="Decline the offer for the main role by an application_id",
     *   operationId="declineOfferForMainRole",
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
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               @OA\Property(property="reason", type="string", description="reason of rejection", example="Some message will be here..."),
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
    public function rejectApproval(Application $application, Request $request): Fractal
    {
        $this->validate($request, [
            'reason' => ['nullable', 'string', new PreventXssRule()],
        ]);

        (new ApplicationCheckRights())->handle($application);
        (new ApplicationRejectApproval())->handle($application, $request->get('reason'));

        return fractal($application, new ApplicationTransformer(true))->serializeWith(new ArraySerializer());
    }
}
