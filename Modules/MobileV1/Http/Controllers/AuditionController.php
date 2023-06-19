<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Models\Application;
use App\Rules\PreventXssRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\MobileV1\Actions\Audition\AuditionAccept;
use Modules\MobileV1\Actions\Audition\AuditionDirectMessage;
use Modules\MobileV1\Actions\Audition\AuditionReject;
use Modules\MobileV1\Actions\Audition\AuditionRightCheck;
use Modules\MobileV1\Transformers\AuditionTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class AuditionController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/auditions/{application_id}/detail",
     *   tags={"Audition"},
     *   summary="Get an audition detail",
     *   description="Get an audition detail",
     *   operationId="getAuditionDetail",
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
    public function auditionDetails(Application $application): Fractal
    {
        $this->rightsCheck($application);

        abort_unless($application->audition, Response::HTTP_NOT_FOUND);

        $application->audition->load([
            'application.role.project',
            'application.role.roleMaterials.materialType',
            'auditionMaterials.materialType',
        ]);

        return fractal($application->audition, new AuditionTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/auditions/{application_id}/accept",
     *   tags={"Audition"},
     *   summary="Change status to accepted",
     *   description="Change the status to: accepted for audition by application id",
     *   operationId="changeAuditionStatusToAccepted",
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
    public function accept(Application $application, AuditionAccept $action): Fractal
    {
        $this->rightsCheck($application);

        abort_unless($application->audition, Response::HTTP_NOT_FOUND);

        $action->handle($application);

        return fractal($application->audition, new AuditionTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/auditions/{application_id}/reject",
     *   tags={"Audition"},
     *   summary="Change status to reject",
     *   description="Change the status to: reject for audition by application id",
     *   operationId="changeAuditionStatusToRejected",
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
    public function reject(Application $application, Request $request, AuditionReject $action): Fractal
    {
        $this->validate($request, [
            'reason' => ['nullable', 'string', new PreventXssRule()],
        ]);

        $this->rightsCheck($application);

        abort_unless($application->audition, Response::HTTP_NOT_FOUND);

        $action->handle($application, $request->get('reason'));

        return fractal($application->audition, new AuditionTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/auditions/{application_id}/direct-message",
     *   tags={"Audition"},
     *   summary="Send a direct message",
     *   description="Send a direct message for project owner",
     *   operationId="sendAuditionDirectMessage",
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
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               @OA\Property(property="message", type="string", description="message for project owner", example="Some message will be here..."),
     *           )
     *       )
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
    public function directMessage(Application $application, Request $request, AuditionDirectMessage $action): JsonResponse
    {
        $this->validate($request, [
            'message' => ['required', 'string', new PreventXssRule()],
        ]);

        $this->rightsCheck($application);

        abort_unless($application->audition, Response::HTTP_NOT_FOUND);

        $action->handle($application, $request->get('message'));

        return response()->json(['message' => __('Your message has been successfully sent!')]);
    }

    private function rightsCheck(Application $application): void
    {
        app(AuditionRightCheck::class)->handle($application);
    }
}
