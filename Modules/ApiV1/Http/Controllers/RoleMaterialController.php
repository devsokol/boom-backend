<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\Role;
use App\Models\RoleMaterial;
use App\Transformers\RoleMaterialTransformer;
use Illuminate\Http\JsonResponse;
use Modules\ApiV1\Actions\Role\RoleByIdOrFail;
use Modules\ApiV1\Actions\RoleMaterial\RoleMaterialCheckRights;
use Modules\ApiV1\Actions\RoleMaterial\RoleMaterialCreate;
use Modules\ApiV1\Actions\RoleMaterial\RoleMaterialRelations;
use Modules\ApiV1\Actions\RoleMaterial\RoleMaterialUpdate;
use Modules\ApiV1\Http\Requests\RoleMaterialRequest;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class RoleMaterialController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/role-materials/{id}/role",
     *   tags={"RoleMaterial"},
     *   summary="Fetch role materials",
     *   description="Fetch role materials",
     *   operationId="fetchRoleMaterials",
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
        $role = (new RoleByIdOrFail())->handle((int) $id);

        (new RoleMaterialCheckRights())->handle($role);

        return fractal($role->roleMaterials, new RoleMaterialTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/role-materials/{id}",
     *   tags={"RoleMaterial"},
     *   summary="Fetch a role material",
     *   description="Fetch a role material by ID",
     *   operationId="fetchMaterial",
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
        $roleMaterial = RoleMaterial::findOrFail((int) $id);

        (new RoleMaterialCheckRights())->handle($roleMaterial);
        (new RoleMaterialRelations())->handle($roleMaterial);

        return fractal($roleMaterial, new RoleMaterialTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/role-materials/{id}/role",
     *   tags={"RoleMaterial"},
     *   summary="Create a new role material",
     *   description="Create a new role material",
     *   operationId="createRoleMaterial",
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
    public function store(string $id, RoleMaterialRequest $request): JsonResponse
    {
        $role = Role::findOrFail((int) $id);

        (new RoleMaterialCheckRights())->handle($role);

        $roleMaterial = (new RoleMaterialCreate())->handle($role, $request->validated());

        return fractal($roleMaterial, new RoleMaterialTransformer())
            ->serializeWith(new ArraySerializer())
            ->respond(201, []);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/role-materials/{id}/update",
     *   tags={"RoleMaterial"},
     *   summary="Update a material",
     *   description="Update a material",
     *   operationId="updateRoleMaterial",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="id",
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
    public function update(string $id, RoleMaterialRequest $request): Fractal
    {
        $roleMaterial = RoleMaterial::findOrFail((int) $id);

        (new RoleMaterialCheckRights())->handle($roleMaterial);
        (new RoleMaterialUpdate())->handle($roleMaterial, $request->validated());

        return fractal($roleMaterial, new RoleMaterialTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Delete(path="/role-materials/{id}",
     *   tags={"RoleMaterial"},
     *   summary="Delete a role material",
     *   description="Delete a role material",
     *   operationId="deleteRoleMaterial",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     description="id",
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
        $roleMaterial = RoleMaterial::findOrFail((int) $id);

        (new RoleMaterialCheckRights())->handle($roleMaterial);

        $roleMaterial->delete();

        return response()->json([], 204);
    }
}
