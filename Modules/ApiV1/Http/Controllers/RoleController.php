<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Dto\RoleData;
use App\Enums\RoleStatus;
use App\Models\Project;
use App\Models\Role;
use App\Rules\HexColorRule;
use App\Services\Common\Attachment\AttachmentService;
use App\Transformers\RoleTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use League\Fractal\Serializer\JsonApiSerializer;
use Modules\ApiV1\Actions\Role\GenerateDynamicLinkForRole;
use Modules\ApiV1\Actions\Role\RoleCheckRights;
use Modules\ApiV1\Actions\Role\RoleCreate;
use Modules\ApiV1\Actions\Role\RoleList;
use Modules\ApiV1\Actions\Role\RoleMakeArchive;
use Modules\ApiV1\Actions\Role\RoleMakeRestore;
use Modules\ApiV1\Actions\Role\RoleRelations;
use Modules\ApiV1\Actions\Role\RoleUpdate;
use Modules\ApiV1\Actions\Role\RoleUpdateLabelColorRange;
use Modules\ApiV1\Http\Requests\RoleRequest;
use Modules\ApiV1\Transformers\RoleListTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class RoleController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Get(path="/projects/{project_id}/roles",
     *   tags={"Role"},
     *   summary="Fetch roles",
     *   description="Fetch roles",
     *   operationId="fetchRoles",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *       name="project_id",
     *       required=true,
     *       in="path",
     *       description="id",
     *
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *
     *   @OA\Parameter(
     *       name="filterByCompanies[]",
     *       in="query",
     *       description="array of ids",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByGenres[]",
     *       in="query",
     *       required=false,
     *
     *       @OA\Schema(
     *         type="array",
     *
     *         @OA\Items(type="integer"),
     *       ),
     *       style="form"
     *   ),
     *
     *   @OA\Parameter(
     *       name="filterByStatus",
     *       in="query",
     *       description="status ID",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByCountry",
     *       in="query",
     *       description="Country ID",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByLocations[]",
     *       in="query",
     *       description="array of cities",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByAddress",
     *       in="query",
     *       description="Address",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByActingGender",
     *       in="query",
     *       description="Acting gender ID",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByCurrency",
     *       in="query",
     *       description="Currency ID",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterPaymentTypeExcludeIds[]",
     *       in="query",
     *       description="Payment type exclude IDs",
     *       required=false,
     *
     *       @OA\Schema(
     *         type="array",
     *
     *         @OA\Items(type="integer"),
     *       ),
     *       style="form"
     *   ),
     *
     *   @OA\Parameter(
     *       name="filterByPaymentType",
     *       in="query",
     *       description="Payment type ID",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="disableSpecificPaymentTypes",
     *       in="query",
     *       description="Payment types that contain a field 'is_single' will be excluded",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByRate[min]",
     *       in="query",
     *       description="Min rate",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByRate[max]",
     *       in="query",
     *       description="Max rate",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByPickShootingDates[0]",
     *       in="query",
     *       description="Start date (2022-08-31)",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByPickShootingDates[1]",
     *       in="query",
     *       description="End date (2022-09-31)",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByApplicationDeadline[0]",
     *       in="query",
     *       description="Start date (2022-08-31)",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="filterByApplicationDeadline[1]",
     *       in="query",
     *       description="End date (2022-09-31)",
     *       required=false
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
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function index(Project $project, Request $request): array
    {
        (new RoleCheckRights())->handle($project);

        return Role::makeCacheByUniqueRequest(function () use ($project, $request) {
            $roles = (new RoleList())->handle($project, $request->all());

            return fractal($roles, new RoleListTransformer())
                ->withResourceName('role')
                ->serializeWith(new JsonApiSerializer());
        });
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Get(path="/projects/{project_id}/roles/{role_id}",
     *   tags={"Role"},
     *   summary="Fetch a role",
     *   description="Fetch a role by project & role ID",
     *   operationId="fetchRole",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *       name="project_id",
     *       required=true,
     *       in="path",
     *       description="id",
     *
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *
     *   @OA\Parameter(
     *       name="role_id",
     *       required=true,
     *       in="path",
     *       description="id",
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
    public function show(Project $project, Role $role): Fractal
    {
        (new RoleCheckRights())->handle($project);
        (new RoleRelations())->handle($role);

        return fractal($role, new RoleTransformer(true))->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Post(path="/projects/{id}/roles",
     *   tags={"Role"},
     *   summary="create a role",
     *   description="Create a role",
     *   operationId="createRole",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *       name="id",
     *       required=true,
     *       in="path",
     *       description="Project ID",
     *
     *       @OA\Schema(
     *           type="string"
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
     *                   "name",
     *                   "description",
     *                   "min_age",
     *                   "max_age",
     *                   "payment_type_id",
     *                   "payment_type_id"
     *               },
     *
     *               @OA\Property(
     *                   property="name",
     *                   description="Name",
     *                   type="string",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                   property="description",
     *                   description="Description",
     *                   type="string",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                   property="acting_gender[]",
     *                   description="Acting gender: Male - 0; Female - 1; Other - 2;",
     *               ),
     *               @OA\Property(
     *                   property="min_age",
     *                   description="Min age",
     *                   type="integer",
     *                   example=18
     *               ),
     *               @OA\Property(
     *                   property="max_age",
     *                   description="Max age",
     *                   type="integer",
     *                   example=64
     *               ),
     *               @OA\Property(
     *                   property="personal_skills[]",
     *                   description="Personal skills ID",
     *                   type="integer",
     *                   maxLength=255
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
     *               @OA\Property(
     *                   property="status",
     *                   description="Status: Public - 0; Private - 1;",
     *                   enum={"0", "1"},
     *               ),
     *               @OA\Property(
     *                   property="rate",
     *                   description="Rate",
     *                   type="integer",
     *               ),
     *               @OA\Property(
     *                   property="city",
     *                   description="City",
     *                   type="string",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                   property="address",
     *                   description="Address",
     *                   type="string",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                   property="pick_shooting_date_type",
     *                   description="Types: Single - 0; Multi - 1; Range -2;",
     *                   type="integer",
     *                   example="0"
     *               ),
     *               @OA\Property(
     *                   property="pick_shooting_dates[][date]",
     *                   description="Pick shooting dates",
     *                   pattern="/([0-9]{4})-(?:[0-9]{2})-([0-9]{2})/",
     *                   type="date",
     *                   example="2022-08-31"
     *               ),
     *               @OA\Property(
     *                   property="application_deadline",
     *                   description="Application deadline",
     *                   pattern="/([0-9]{4})-(?:[0-9]{2})-([0-9]{2})/",
     *                   type="date",
     *                   example="2022-08-31"
     *               ),
     *               @OA\Property(
     *                   property="currency_id",
     *                   description="Currency ID",
     *                   type="integer",
     *               ),
     *               @OA\Property(
     *                   property="payment_type_id",
     *                   description="Payment type ID",
     *                   type="integer",
     *               ),
     *               @OA\Property(
     *                   property="ethnicity_id",
     *                   description="Ethnicity ID",
     *                   type="integer",
     *               ),
     *               @OA\Property(
     *                   property="country_id",
     *                   description="Country ID",
     *                   type="integer",
     *               ),
     *           )
     *       )
     *   ),
     *
     *   @OA\Response(
     *     response=201,
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
    public function store(
        Project $project,
        RoleRequest $request,
        AttachmentService $attachmentService
    ): JsonResponse {
        (new RoleCheckRights())->handle($project);

        $roleData = RoleData::from($request->validated());

        $role = (new RoleCreate($attachmentService))->handle($roleData, $project);

        (new RoleRelations())->handle($role);

        return fractal($role, new RoleTransformer())->serializeWith(new ArraySerializer())->respond(201, []);
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Post(path="/projects/{project_id}/roles/{role_id}",
     *   tags={"Role"},
     *   summary="Update a role",
     *   description="Update a role",
     *   operationId="updateRole",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *       name="project_id",
     *       required=true,
     *       in="path",
     *       description="Project ID",
     *
     *       @OA\Schema(
     *           type="string"
     *       )
     *   ),
     *
     *   @OA\Parameter(
     *       name="role_id",
     *       required=true,
     *       in="path",
     *       description="Role ID",
     *
     *       @OA\Schema(
     *           type="string"
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
     *                   "name",
     *                   "description",
     *                   "min_age",
     *                   "max_age",
     *                   "payment_type_id",
     *                   "payment_type_id"
     *               },
     *
     *               @OA\Property(
     *                   property="_method",
     *                   description="Request method",
     *                   type="string",
     *                   maxLength=255,
     *                   default="PUT"
     *               ),
     *               @OA\Property(
     *                   property="name",
     *                   description="Name",
     *                   type="string",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                   property="description",
     *                   description="Description",
     *                   type="string",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                   property="acting_gender[]",
     *                   description="Acting gender: Male - 0; Female - 1; Other - 2;",
     *               ),
     *               @OA\Property(
     *                   property="min_age",
     *                   description="Min age",
     *                   type="integer",
     *                   example=18
     *               ),
     *               @OA\Property(
     *                   property="max_age",
     *                   description="Max age",
     *                   type="integer",
     *                   example=64
     *               ),
     *               @OA\Property(
     *                   property="personal_skills[]",
     *                   description="Personal skills ID",
     *                   type="integer",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                   property="status",
     *                   description="Status: Public - 0; Private - 1;",
     *                   enum={"0", "1"},
     *               ),
     *               @OA\Property(
     *                   property="rate",
     *                   description="Rate",
     *                   type="integer",
     *               ),
     *               @OA\Property(
     *                   property="city",
     *                   description="City",
     *                   type="string",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                   property="address",
     *                   description="Address",
     *                   type="string",
     *                   maxLength=255
     *               ),
     *               @OA\Property(
     *                   property="pick_shooting_dates[][date]",
     *                   description="Pick shooting dates",
     *                   pattern="/([0-9]{4})-(?:[0-9]{2})-([0-9]{2})/",
     *                   type="date",
     *                   example="2022-08-31"
     *               ),
     *               @OA\Property(
     *                   property="application_deadline",
     *                   description="Application deadline",
     *                   pattern="/([0-9]{4})-(?:[0-9]{2})-([0-9]{2})/",
     *                   type="date",
     *                   example="2022-08-31"
     *               ),
     *               @OA\Property(
     *                   property="currency_id",
     *                   description="Currency ID",
     *                   type="integer",
     *               ),
     *               @OA\Property(
     *                   property="payment_type_id",
     *                   description="Payment type ID",
     *                   type="integer",
     *               ),
     *               @OA\Property(
     *                   property="ethnicity_id",
     *                   description="Ethnicity ID",
     *                   type="integer",
     *               ),
     *               @OA\Property(
     *                   property="country_id",
     *                   description="Country ID",
     *                   type="integer",
     *               ),
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
    public function update(Project $project, Role $role, RoleRequest $request): Fractal
    {
        (new RoleCheckRights())->handle($project);

        $roleData = RoleData::from($request->validated());

        $role = (new RoleUpdate())->handle($roleData, $role);

        (new RoleRelations())->handle($role);

        return fractal($role, new RoleTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Delete(path="/projects/{project_id}/roles/{role_id}",
     *   tags={"Role"},
     *   summary="Delete a role",
     *   description="Delete a role",
     *   operationId="deleteRole",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *     name="project_id",
     *     required=true,
     *     in="path",
     *     description="id",
     *
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *
     *   @OA\Parameter(
     *     name="role_id",
     *     required=true,
     *     in="path",
     *     description="id",
     *
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *
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
     *
     * @codingStandardsIgnoreEnd
     */
    public function destroy(Project $project, Role $role): JsonResponse
    {
        (new RoleCheckRights())->handle($project);

        $role->delete();

        return response()->json([], 204);
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Post(path="/projects/{project_id}/roles/{role_id}/label-color-range",
     *   tags={"Role"},
     *   summary="Update a label color-range",
     *   description="Update a label color-range",
     *   operationId="updateRoleLabelColorRange",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *       name="project_id",
     *       required=true,
     *       in="path",
     *       description="id",
     *
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *
     *   @OA\Parameter(
     *       name="role_id",
     *       required=true,
     *       in="path",
     *       description="id",
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
     *
     *               @OA\Property(property="dark_color", type="string", description="Dark HEX color", example="#00d49b"),
     *               @OA\Property(property="light_color", type="string", description="Light HEX color", example="#6efa82"),
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
    public function updateLabelColorRange(Project $project, Role $role, Request $request): Fractal
    {
        (new RoleCheckRights())->handle($project);

        $this->validate($request, [
            'dark_color' => ['nullable', new HexColorRule()],
            'light_color' => ['nullable', new HexColorRule()],
        ]);

        (new RoleUpdateLabelColorRange())->handle($role, $request->dark_color, $request->light_color);

        return fractal($role, new RoleTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Post(path="/roles/{id}/archive",
     *   tags={"Role"},
     *   summary="Archive a role",
     *   description="Archive a role",
     *   operationId="archiveRole",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     description="id",
     *
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated. / This action is unauthorized."
     *   ),
     *   @OA\Response(response=403, description="This action is unauthorized."),
     *   @OA\Response(response=404, description="Not found")
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function archive(Role $role): Fractal
    {
        (new RoleCheckRights())->handle($role);

        $role->update(['status' => RoleStatus::ARCHIVE->value]);

        (new RoleMakeArchive())->handle($role);
        (new RoleRelations())->handle($role);

        return fractal($role, new RoleTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Post(path="/roles/{id}/restore",
     *   tags={"Role"},
     *   summary="Restore a role",
     *   description="Restore a role",
     *   operationId="restoreRole",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *     name="id",
     *     required=true,
     *     in="path",
     *     description="id",
     *
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *
     *   @OA\RequestBody(
     *       required=true,
     *
     *       @OA\MediaType(
     *           mediaType="application/json",
     *
     *           @OA\Schema(
     *
     *               @OA\Property(property="status", type="string", description="status public - 0 or private - 1", example="0"),
     *           )
     *       )
     *   ),
     *
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated. / This action is unauthorized."
     *   ),
     *   @OA\Response(response=403, description="This action is unauthorized."),
     *   @OA\Response(response=404, description="Not found")
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function restore(int $roleId, Request $request): Fractal
    {
        $role = Role::archived()->findOrFail($roleId);

        (new RoleCheckRights())->handle($role);

        $this->validate($request, [
            'status' => ['required', Rule::in(RoleStatus::PUBLIC->value, RoleStatus::PRIVATE->value)],
        ]);

        (new RoleMakeRestore())->handle($role);
        (new RoleRelations())->handle($role);

        return fractal($role, new RoleTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Get(path="/roles/{role_id}/renew-dynamic-link",
     *   tags={"Role"},
     *   summary="Renew a dynamic link",
     *   description="Renew a dynamic link",
     *   operationId="renewDynamicLink",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *     name="role_id",
     *     required=true,
     *     in="path",
     *     description="id",
     *
     *     @OA\Schema(
     *         type="string"
     *     )
     *   ),
     *
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
     *
     * @codingStandardsIgnoreEnd
     */
    public function renewDynamicLink(Role $role): JsonResponse
    {
        (new RoleCheckRights())->handle($role);

        return response()->json([
            'dynamic_link' => (new GenerateDynamicLinkForRole())->handle($role, updateModel: true),
        ]);
    }
}
