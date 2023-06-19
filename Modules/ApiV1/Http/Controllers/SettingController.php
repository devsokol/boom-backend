<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Transformers\SettingTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class SettingController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/settings",
     *   tags={"Setting"},
     *   summary="Get general settings",
     *   description="Get general settings",
     *   operationId="getSettings",
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent()
     *   ),
     * )
     * @codingStandardsIgnoreEnd
     */
    public function __invoke(): Fractal
    {
        return fractal([true], new SettingTransformer())->serializeWith(new ArraySerializer());
    }
}
