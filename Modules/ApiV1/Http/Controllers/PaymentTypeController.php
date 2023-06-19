<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\PaymentType;
use App\Transformers\PaymentTypeTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class PaymentTypeController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/payment-types",
     *   tags={"PaymentType"},
     *   summary="Get payment types",
     *   description="Get payment type list. When a is_single field is set to TRUE, then the fields: rate, currency are not included in the filtering.",
     *   operationId="getPaymentTypes",
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
        $paymentTypes = PaymentType::makeCacheByUniqueRequest(function () {
            return PaymentType::orderBy('id')->get();
        });

        return fractal($paymentTypes, new PaymentTypeTransformer())->serializeWith(new ArraySerializer());
    }
}
