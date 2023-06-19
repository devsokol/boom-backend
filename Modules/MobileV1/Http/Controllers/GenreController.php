<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Models\Genre;
use App\Transformers\GenreTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class GenreController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/genres",
     *   tags={"Genre"},
     *   summary="Get genres",
     *   description="Get genre list",
     *   operationId="getGenres",
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
     *       response=404,
     *       description="Not Found"
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     * )
     * @codingStandardsIgnoreEnd
     */
    public function __invoke(): Fractal
    {
        $genres = Genre::makeCacheByUniqueRequest(function () {
            return Genre::all();
        });

        return fractal($genres, new GenreTransformer())->serializeWith(new ArraySerializer());
    }
}
