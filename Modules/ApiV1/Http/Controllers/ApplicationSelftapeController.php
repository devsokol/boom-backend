<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Dto\ApplicationSelftapeData;
use App\Models\Application;
use App\Services\Common\Attachment\AttachmentService;
use Modules\ApiV1\Actions\Application\ApplicationCheckingWhetherRequestIsAllowed;
use Modules\ApiV1\Actions\ApplicationSelftape\ApplicationSelftapeCheckRights;
use Modules\ApiV1\Actions\ApplicationSelftape\ApplicationSelftapeStore;
use Modules\ApiV1\Actions\ApplicationSelftape\ApplicationSelftapeUpdate;
use Modules\ApiV1\Http\Requests\ApplicationSelftapeRequest;
use Modules\ApiV1\Http\Requests\UpdateApplicationSelftapeRequest;
use Modules\ApiV1\Transformers\ApplicationSelftapeTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class ApplicationSelftapeController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Get(path="/applications/{application_id}/selftape-request",
     *   tags={"ApplicationSelftape"},
     *   summary="Get an application selftape request",
     *   description="Get an application selftape request",
     *   operationId="getApplicationSelftapeRequest",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *       name="application_id",
     *       required=true,
     *       in="path",
     *       description="Application ID",
     *
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *
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
     *
     * @codingStandardsIgnoreEnd
     */
    public function show(Application $application): Fractal
    {
        (new ApplicationSelftapeCheckRights())->handle($application);

        return fractal(
            $application->applicationSelftape,
            new ApplicationSelftapeTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Post(path="/applications/{application_id}/selftape-request",
     *   tags={"ApplicationSelftape"},
     *   summary="Application selftape request",
     *   description="Application selftape request",
     *   operationId="createApplicationSelftapeRequest",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *       name="application_id",
     *       required=true,
     *       in="path",
     *       description="Application ID",
     *
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *
     *   @OA\RequestBody(
     *
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *
     *           @OA\Schema(
     *               required={
     *                   "description",
     *                   "deadline_datetime"
     *               },
     *
     *               @OA\Property(
     *                   property="description",
     *                   description="Description",
     *                   type="string",
     *                   maxLength=255,
     *                   example="Some text will be here..."
     *               ),
     *               @OA\Property(
     *                   property="deadline_datetime",
     *                   description="Deadline datetime",
     *                   type="string",
     *                   example="2022-10-03 14:20"
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
     *
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
     *
     * @codingStandardsIgnoreEnd
     */
    public function store(
        ApplicationSelftapeRequest $request,
        Application $application,
        AttachmentService $attachmentService
    ): Fractal {
        (new ApplicationCheckingWhetherRequestIsAllowed())->handle($application);

        (new ApplicationSelftapeCheckRights())->handle($application);

        $applicationSelftapeData = ApplicationSelftapeData::from($request->validated());

        $applicationSelftape = (new ApplicationSelftapeStore($attachmentService))->handle($applicationSelftapeData, $application);

        return fractal(
            $applicationSelftape,
            new ApplicationSelftapeTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Put(path="/applications/{application_id}/selftape-request",
     *   tags={"ApplicationSelftape"},
     *   summary="Update a application selftape",
     *   description="Update a application selftape",
     *   operationId="updateApplicationSelftape",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *       name="application_id",
     *       required=true,
     *       in="path",
     *       description="Application ID",
     *
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *
     *   @OA\RequestBody(
     *       required=true,
     *
     *       @OA\MediaType(
     *           mediaType="application/json",
     *
     *           @OA\Schema(
     *               required={"description", "deadline_datetime"},
     *
     *               @OA\Property(property="description", type="string", description="description", example="Some text..."),
     *               @OA\Property(property="deadline_datetime", type="string", description="deadline datetime", example="2022-10-03 16:30:00"),
     *           )
     *       )
     *   ),
     *
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *
     *     @OA\JsonContent()
     *   ),
     *
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Unprocessable Entity"
     *   )
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function update(UpdateApplicationSelftapeRequest $request, Application $application): Fractal
    {
        (new ApplicationSelftapeCheckRights())->handle($application);
        (new ApplicationSelftapeUpdate())->handle($application, $request->validated());

        return fractal(
            $application->applicationSelftape,
            new ApplicationSelftapeTransformer()
        )->serializeWith(new ArraySerializer());
    }
}
