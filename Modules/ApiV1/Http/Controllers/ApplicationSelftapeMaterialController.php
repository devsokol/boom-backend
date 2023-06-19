<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\ApplicationSelftape;
use App\Models\ApplicationSelftapeMaterial;
use App\Transformers\ApplicationSelftapeMaterialTransformer;
use Illuminate\Http\JsonResponse;
use Modules\ApiV1\Actions\ApplicationSelftape\ApplicationSelftapeByIdOrFail;
use Modules\ApiV1\Actions\ApplicationSelftapeMaterial\ApplicationSelftapeMaterialCheckRights;
use Modules\ApiV1\Actions\ApplicationSelftapeMaterial\ApplicationSelftapeMaterialRelations;
use Modules\ApiV1\Actions\ApplicationSelftapeMaterial\ApplicationSelftapeMaterialStore;
use Modules\ApiV1\Actions\ApplicationSelftapeMaterial\ApplicationSelftapeMaterialUpdate;
use Modules\ApiV1\Http\Requests\ApplicationSelftapeMaterialRequest;
use Modules\ApiV1\Transformers\ApplicationSelftapeTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class ApplicationSelftapeMaterialController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/application-selftape-materials/{application_selftape_id}/application-selftape",
     *   tags={"ApplicationSelftapeMaterial"},
     *   summary="Fetch application selftape materials",
     *   description="Fetch application selftape materials",
     *   operationId="fetchApplicationSelftapeMaterials",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="application_selftape_id",
     *       required=true,
     *       in="path",
     *       description="Application selftape ID",
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
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function index(string $id): Fractal
    {
        $applicationSelftape = (new ApplicationSelftapeByIdOrFail())->handle((int) $id);

        (new ApplicationSelftapeMaterialCheckRights())->handle($applicationSelftape);

        return fractal(
            $applicationSelftape,
            new ApplicationSelftapeTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/application-selftape-materials/{application_selftape_material_id}",
     *   tags={"ApplicationSelftapeMaterial"},
     *   summary="Fetch an application type material",
     *   description="Fetch an application type material by ID",
     *   operationId="fetchApplicationSelftapeMaterial",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="application_selftape_material_id",
     *       required=true,
     *       in="path",
     *       description="Application selftape material ID",
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
    public function show(string $id): Fractal
    {
        $applicationSelftapeMaterial = ApplicationSelftapeMaterial::findOrFail((int) $id);

        (new ApplicationSelftapeMaterialCheckRights())->handle($applicationSelftapeMaterial);
        (new ApplicationSelftapeMaterialRelations())->handle($applicationSelftapeMaterial);

        return fractal(
            $applicationSelftapeMaterial,
            new ApplicationSelftapeMaterialTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/application-selftape-materials/{application_selftape_id}/audition",
     *   tags={"ApplicationSelftapeMaterial"},
     *   summary="Create a new application selftape material",
     *   description="Create a new application selftape material",
     *   operationId="createApplicationSelftapeMaterial",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="application_selftape_id",
     *       required=true,
     *       in="path",
     *       description="Application selftape ID",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *               required={"material_type_id", "attachment"},
     *               @OA\Property(
     *                   property="material_type_id",
     *                   description="Material type ID",
     *                   type="integer",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                    description="Material attachment",
     *                    property="attachment",
     *                    type="file"
     *               ),
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *     response=201,
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
     *       response=422,
     *       description="Unprocessable Entity"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function store(string $id, ApplicationSelftapeMaterialRequest $request): JsonResponse
    {
        $applicationSelftape = ApplicationSelftape::findOrFail((int) $id);

        (new ApplicationSelftapeMaterialCheckRights())->handle($applicationSelftape);

        $applicationSelftapeMaterial = (new ApplicationSelftapeMaterialStore())
            ->handle($applicationSelftape, $request->validated());

        return fractal($applicationSelftapeMaterial, new ApplicationSelftapeMaterialTransformer())
            ->serializeWith(new ArraySerializer())
            ->respond(201, []);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/application-selftape-materials/{application_selftape_material_id}/update",
     *   tags={"ApplicationSelftapeMaterial"},
     *   summary="Update an application selftape material",
     *   description="Update an application selftape material",
     *   operationId="updateApplicationSelftapeMaterial",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="application_selftape_material_id",
     *       required=true,
     *       in="path",
     *       description="Application selftape material ID",
     *       @OA\Schema(
     *           type="string"
     *       )
     *   ),
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *               required={"material_type_id", "attachment"},
     *               @OA\Property(
     *                   property="material_type_id",
     *                   description="Material type ID",
     *                   type="integer",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                    description="Material attachment",
     *                    property="attachment",
     *                    type="file"
     *               ),
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
     *       response=422,
     *       description="Unprocessable Entity"
     *   )
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function update(string $id, ApplicationSelftapeMaterialRequest $request): Fractal
    {
        $applicationSelftapeMaterial = ApplicationSelftapeMaterial::findOrFail((int) $id);

        (new ApplicationSelftapeMaterialCheckRights())->handle($applicationSelftapeMaterial);
        (new ApplicationSelftapeMaterialUpdate())->handle($applicationSelftapeMaterial, $request->validated());

        return fractal(
            $applicationSelftapeMaterial,
            new ApplicationSelftapeMaterialTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Delete(path="/application-selftape-materials/{application_selftape_material_id}",
     *   tags={"ApplicationSelftapeMaterial"},
     *   summary="Delete an application selftape material",
     *   description="Delete a application selftape material",
     *   operationId="deleteApplicationSelftapeMaterial",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *     name="application_selftape_material_id",
     *     required=true,
     *     in="path",
     *     description="Application selftape material ID",
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
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
    public function destroy(string $id): JsonResponse
    {
        $applicationSelftapeMaterial = ApplicationSelftapeMaterial::findOrFail((int) $id);

        (new ApplicationSelftapeMaterialCheckRights())->handle($applicationSelftapeMaterial);

        $applicationSelftapeMaterial->delete();

        return response()->json([], 204);
    }
}
