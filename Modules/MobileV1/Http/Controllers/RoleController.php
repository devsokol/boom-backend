<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Models\Role;
use App\Services\Common\Role\FilterRoleService;
use App\Transformers\RoleTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\JsonApiSerializer;
use Modules\MobileV1\Actions\Role\RoleAddBookmark;
use Modules\MobileV1\Actions\Role\RoleBookmarks;
use Modules\MobileV1\Actions\Role\RoleList;
use Modules\MobileV1\Actions\Role\RoleLoadOnlyActorApplications;
use Modules\MobileV1\Actions\Role\RoleRelations;
use Modules\MobileV1\Actions\Role\RoleRemoveBookmark;
use Modules\MobileV1\Http\Requests\MinMaxPriceRequest;
use Modules\MobileV1\Transformers\RoleListTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class RoleController extends BaseController
{
    private FilterRoleService $filterRoleService;

    public function __construct(FilterRoleService $filterRoleService)
    {
        $this->filterRoleService = $filterRoleService;
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/roles",
     *   tags={"Role"},
     *   summary="Fetch roles",
     *   description="Fetch roles",
     *   operationId="fetchRoles",
     *   security={ {"bearerAuth": {} }},
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
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(type="integer"),
     *       ),
     *       style="form"
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
     *       @OA\Schema(
     *         type="array",
     *         @OA\Items(type="integer"),
     *       ),
     *       style="form"
     *   ),
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
     *
     * @todo need optimization query, cache with is_saved!
     */
    public function index(Request $request, RoleList $action): Fractal
    {
        $paginator = $action->handle($request->except([
            'filterByStatus',
        ]));

        $roles = $paginator->getCollection();

        return fractal($roles, new RoleListTransformer())
            ->withResourceName('role')
            ->serializeWith(new JsonApiSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator));
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/roles/filter-tools",
     *   tags={"Role"},
     *   description="Getting multiple filter lists",
     *   operationId="fetchFilterTools",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="currency_id",
     *       in="query",
     *       description="Currency ID",
     *       required=false
     *   ),
     *   @OA\Parameter(
     *       name="payment_type_id",
     *       in="query",
     *       description="Payment type ID",
     *       required=false
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
    public function filterTools(Request $request): JsonResponse
    {
        $data = $this->filterRoleService->tools($request->currency_id, $request->payment_type_id);

        return response()->json($data);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/roles/{id}",
     *   tags={"Role"},
     *   summary="Fetch a role",
     *   description="Fetch a role by role ID",
     *   operationId="fetchRole",
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
    public function show(Role $role): Fractal
    {
        (new RoleRelations)->handle($role);
        (new RoleLoadOnlyActorApplications)->handle($role);

        return fractal($role, new RoleTransformer(true))->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/roles/min-max-rate",
     *   tags={"Role"},
     *   summary="Fetch min rate/max rate",
     *   description="Fetch the maximum/minimum rate of the roles",
     *   operationId="fetchRoleMinMaxRate",
     *   security={ {"bearerAuth": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"currency_id", "payment_type_id"},
     *               @OA\Property(property="currency_id", type="integer", description="currency ID", example="2"),
     *               @OA\Property(property="payment_type_id", type="integer", description="payment ype ID", example="1"),
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
    public function minMaxRate(MinMaxPriceRequest $request): JsonResponse
    {
        $prices = Role::makeCacheByUniqueRequest(function () use ($request) {
            return $this->filterRoleService->minMaxRate($request->currency_id, $request->payment_type_id);
        });

        return response()->json($prices);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/roles/cities",
     *   tags={"Role"},
     *   summary="Fetch role cities",
     *   description="Fetch role cities",
     *   operationId="fetchRoleCities",
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
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   ),
     * )
     * @codingStandardsIgnoreEnd
     */
    public function cities(): JsonResponse
    {
        $cities = Role::makeCacheByUniqueRequest(function () {
            return $this->filterRoleService->getCitiesList();
        });

        return response()->json($cities);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/roles/bookmarks",
     *   tags={"Role"},
     *   summary="Fetch roles bookmarks",
     *   description="Fetch roles bookmarks",
     *   operationId="fetchRolesBookmarks",
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
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function bookmarks(RoleBookmarks $action): Fractal
    {
        $paginator = $action->handle();

        $roles = $paginator->getCollection();

        return fractal($roles, new RoleTransformer())
            ->withResourceName('role')
            ->serializeWith(new JsonApiSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator));
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/roles/bookmarks/role/{id}/add",
     *   tags={"Role"},
     *   summary="Add a role to bookmarks",
     *   description="Add a role to bookmarks",
     *   operationId="addRoleToBookmark",
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
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function addBookmark(int $roleId, RoleAddBookmark $action): JsonResponse
    {
        $role = Role::public()->onlyActiveProject()->findOrFail($roleId);

        return response()->json((bool) $action->handle($role));
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/roles/bookmarks/role/{id}/remove",
     *   tags={"Role"},
     *   summary="Remove a role from bookmarks",
     *   description="Remove a role from bookmarks",
     *   operationId="RemoveRoleFromBookmarks",
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
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function removeBookmark(int $roleId, RoleRemoveBookmark $action): JsonResponse
    {
        $role = Role::findOrFail($roleId);

        return response()->json((bool) $action->handle($role));
    }
}
