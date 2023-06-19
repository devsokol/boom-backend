<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\ProjectType;
use App\Transformers\ProjectTypeTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class ProjectTypeController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/project-types",
     *   tags={"ProjectType"},
     *   summary="Get project types",
     *   description="Get project type list",
     *   operationId="getProjectTypeList",
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
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function __invoke(): Fractal
    {
        $projectTypeList = ProjectType::makeCacheByUniqueRequest(function () {
            return ProjectType::all();
        });

        return fractal($projectTypeList, new ProjectTypeTransformer())->serializeWith(new ArraySerializer());
    }
}
