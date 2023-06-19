<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Models\Ethnicity;
use App\Transformers\EthnicityTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class EthnicityController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/ethnicities",
     *   tags={"Ethnicity"},
     *   summary="Get ethnicities",
     *   description="Get ethnicity list",
     *   operationId="getEthnicities",
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
    public function __invoke(): Fractal
    {
        $ethnicities = Ethnicity::makeCacheByUniqueRequest(function () {
            return Ethnicity::all();
        });

        return fractal($ethnicities, new EthnicityTransformer())->serializeWith(new ArraySerializer());
    }
}
