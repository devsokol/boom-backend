<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\Currency;
use App\Transformers\CurrencyTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class CurrencyController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/currencies",
     *   tags={"Currency"},
     *   summary="Get currencies",
     *   description="Get currency list",
     *   operationId="getCurrencies",
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
        $currencies = Currency::makeCacheByUniqueRequest(function () {
            return Currency::all();
        });

        return fractal($currencies, new CurrencyTransformer())->serializeWith(new ArraySerializer());
    }
}
