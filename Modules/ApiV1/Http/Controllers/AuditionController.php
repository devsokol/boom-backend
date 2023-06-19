<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Dto\AuditionData;
use App\Models\Application;
use Modules\ApiV1\Actions\Application\ApplicationCheckingWhetherRequestIsAllowed;
use Modules\ApiV1\Actions\Audition\AuditionCheckRights;
use Modules\ApiV1\Actions\Audition\AuditionLoadRelations;
use Modules\ApiV1\Actions\Audition\AuditionStore;
use Modules\ApiV1\Actions\Audition\AuditionUpdate;
use Modules\ApiV1\Http\Requests\AuditionRequest;
use Modules\ApiV1\Transformers\AuditionTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class AuditionController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/applications/{application_id}/audition-request",
     *   tags={"Audition"},
     *   summary="Get an audition request",
     *   description="Get an audition request",
     *   operationId="getAuditionRequest",
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
        (new AuditionCheckRights())->handle($application);
        (new AuditionLoadRelations())->handle($application->audition);

        return fractal($application->audition, new AuditionTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/applications/{application_id}/audition-request",
     *   tags={"Audition"},
     *   summary="Create an audition request",
     *   description="Create an audition request",
     *   operationId="createAuditionRequest",
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
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *               required={
     *                   "type",
     *                   "address",
     *                   "audition_date",
     *                   "audition_time"
     *               },
     *               @OA\Property(
     *                   property="type",
     *                   description="Types: Offline - 0; Online - 1;",
     *                   enum={"-", "0", "1"}
     *               ),
     *               @OA\Property(
     *                   property="address",
     *                   description="Address",
     *                   type="string",
     *                   maxLength=255,
     *                   example="st. Shelkovichnaya, 23, Kiev, 01024"
     *               ),
     *               @OA\Property(
     *                   property="audition_date",
     *                   description="Audition date",
     *                   type="string",
     *                   example="2022-09-28"
     *               ),
     *               @OA\Property(
     *                   property="audition_time",
     *                   description="Audition time",
     *                   type="string",
     *                   example="15:55"
     *               ),
     *               @OA\Property(
     *                   property="materials[][material_type_id]",
     *                   description="Material type ID",
     *                   type="integer",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                    description="Material attachment",
     *                    property="materials[][attachment]",
     *                    type="file"
     *               ),
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
    public function store(AuditionRequest $request, Application $application): Fractal
    {
        (new ApplicationCheckingWhetherRequestIsAllowed())->handle($application);

        (new AuditionCheckRights())->handle($application);

        $auditionData = AuditionData::from($request->validated());

        $audition = (new AuditionStore())->handle($auditionData, $application);
        (new AuditionLoadRelations())->handle($audition);

        return fractal($audition, new AuditionTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Put(path="/applications/{application_id}/audition-request",
     *   tags={"Audition"},
     *   summary="Update an audition request",
     *   description="Update an audition request",
     *   operationId="updateAuditionRequest",
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
     *                   "type",
     *                   "address",
     *                   "audition_date",
     *                   "audition_time"
     *               },
     *               @OA\Property(
     *                   property="type",
     *                   description="Types: Offline - 0; Online - 1;",
     *                   enum={"0", "1"}
     *               ),
     *               @OA\Property(
     *                   property="address",
     *                   description="Address",
     *                   type="string",
     *                   maxLength=255,
     *                   example="st. Shelkovichnaya, 23, Kiev, 01024"
     *               ),
     *               @OA\Property(
     *                   property="audition_date",
     *                   description="Audition date",
     *                   type="string",
     *                   example="2022-09-28"
     *               ),
     *               @OA\Property(
     *                   property="audition_time",
     *                   description="Audition time",
     *                   type="string",
     *                   example="15:55"
     *               ),
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
    public function update(AuditionRequest $request, Application $application): Fractal
    {
        (new AuditionCheckRights())->handle($application);

        $audition = $application->audition;

        (new AuditionUpdate())->handle($audition, $request->validated());
        (new AuditionLoadRelations())->handle($audition);

        return fractal($audition, new AuditionTransformer())->serializeWith(new ArraySerializer());
    }
}
