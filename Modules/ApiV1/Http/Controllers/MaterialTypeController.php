<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\AttachmentType;
use App\Models\MaterialType;
use App\Transformers\MaterialTypeTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class MaterialTypeController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Get(path="/material-types",
     *   tags={"MaterialType"},
     *   summary="Get material types",
     *   description="Get material type list",
     *   operationId="getMaterialTypeList",
     *   security={ {"bearerAuth": {} }},
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
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function __invoke(): Fractal
    {
        $materialTypeList = AttachmentType::makeCacheByUniqueRequest(function () {
            return AttachmentType::getMaterialTypes();
        });

        return fractal($materialTypeList, new MaterialTypeTransformer())->serializeWith(new ArraySerializer());
    }
}
