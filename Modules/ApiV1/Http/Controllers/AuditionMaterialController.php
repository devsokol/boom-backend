<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\Audition;
use App\Models\AuditionMaterial;
use Illuminate\Http\JsonResponse;
use Modules\ApiV1\Actions\Audition\AuditionByIdOrFail;
use Modules\ApiV1\Actions\AuditionMaterial\AuditionMaterialCheckRights;
use Modules\ApiV1\Actions\AuditionMaterial\AuditionMaterialRelations;
use Modules\ApiV1\Actions\AuditionMaterial\AuditionMaterialStore;
use Modules\ApiV1\Actions\AuditionMaterial\AuditionMaterialUpdate;
use Modules\ApiV1\Http\Requests\AuditionMaterialRequest;
use Modules\ApiV1\Transformers\AuditionMaterialTransformer;
use Modules\ApiV1\Transformers\AuditionTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class AuditionMaterialController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/audition-materials/{audition_id}/audition",
     *   tags={"AuditionMaterial"},
     *   summary="Fetch audition materials",
     *   description="Fetch audition materials",
     *   operationId="fetchAuditionMaterials",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="audition_id",
     *       required=true,
     *       in="path",
     *       description="Audition ID",
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
    public function index(int $id): Fractal
    {
        $audition = (new AuditionByIdOrFail())->handle($id);

        (new AuditionMaterialCheckRights())->handle($audition);

        return fractal($audition, new AuditionTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/audition-materials/{audition_material_id}",
     *   tags={"AuditionMaterial"},
     *   summary="Fetch an audition material",
     *   description="Fetch an audition material by ID",
     *   operationId="fetchAuditionMaterial",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="audition_material_id",
     *       required=true,
     *       in="path",
     *       description="Audition material ID",
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
        $auditionMaterial = AuditionMaterial::findOrFail((int) $id);

        (new AuditionMaterialCheckRights())->handle($auditionMaterial);
        (new AuditionMaterialRelations())->handle($auditionMaterial);

        return fractal($auditionMaterial, new AuditionMaterialTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/audition-materials/{audition_id}/audition",
     *   tags={"AuditionMaterial"},
     *   summary="Create a new audition material",
     *   description="Create a new audition material",
     *   operationId="createAuditionMaterial",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="audition_id",
     *       required=true,
     *       in="path",
     *       description="id",
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
    public function store(string $id, AuditionMaterialRequest $request): JsonResponse
    {
        $audition = Audition::findOrFail((int) $id);

        (new AuditionMaterialCheckRights())->handle($audition);
        $auditionMaterial = (new AuditionMaterialStore())->handle($audition, $request->validated());

        return fractal($auditionMaterial, new AuditionMaterialTransformer())
            ->serializeWith(new ArraySerializer())
            ->respond(201, []);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/audition-materials/{audition_material_id}/update",
     *   tags={"AuditionMaterial"},
     *   summary="Update an audition material",
     *   description="Update an audition material",
     *   operationId="updateAuditionMaterial",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="audition_material_id",
     *       required=true,
     *       in="path",
     *       description="Audition material ID",
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
    public function update(string $id, AuditionMaterialRequest $request): Fractal
    {
        $auditionMaterial = AuditionMaterial::findOrFail((int) $id);

        (new AuditionMaterialCheckRights())->handle($auditionMaterial);
        (new AuditionMaterialUpdate())->handle($auditionMaterial, $request->validated());

        return fractal($auditionMaterial, new AuditionMaterialTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Delete(path="/audition-materials/{audition_material_id}",
     *   tags={"AuditionMaterial"},
     *   summary="Delete an audition material",
     *   description="Delete a audition material",
     *   operationId="deleteAuditionMaterial",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *     name="audition_material_id",
     *     required=true,
     *     in="path",
     *     description="Audition material ID",
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
        $auditionMaterial = AuditionMaterial::findOrFail((int) $id);

        (new AuditionMaterialCheckRights())->handle($auditionMaterial);

        $auditionMaterial->delete();

        return response()->json([], 204);
    }
}
