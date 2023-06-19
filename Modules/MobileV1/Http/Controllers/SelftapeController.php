<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Models\Selftape;
use App\Transformers\SelftapeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Modules\MobileV1\Http\Requests\UploadSelftapeRequest;
use Modules\MobileV1\Services\Actor\ProfileService;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class SelftapeController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/actor/selftapes",
     *   tags={"Actor"},
     *   summary="Upload a selftape",
     *   description="Upload a selftape",
     *   operationId="uploadSelftape",
     *   security={ {"bearerAuth": {} }},
     *   @OA\RequestBody(
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *               required={
     *                   "video[]",
     *               },
     *               @OA\Property(
     *                   description="Selftape",
     *                   property="video[]",
     *                   type="array",
     *                   @OA\Items(type="string", format="binary")
     *               )
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
     * )
     * @codingStandardsIgnoreEnd
     */
    public function store(UploadSelftapeRequest $request, ProfileService $service): Fractal
    {
        $actor = $request->user();

        $selftapes = $service->storeSelftapes($actor, $request->validated()['video']);

        return fractal($selftapes, new SelftapeTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Delete(path="/actor/selftapes/{id}",
     *   tags={"Actor"},
     *   summary="Delete a selftape",
     *   description="Delete a selftape",
     *   operationId="deleteSelftape",
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
    public function destroy(Selftape $selftape): JsonResponse
    {
        Gate::allowIf(fn ($user) => $user->getKey() === $selftape->actor->getKey());

        $selftape->delete();

        return response()->json([], 204);
    }
}
