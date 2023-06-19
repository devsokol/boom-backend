<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Boom API Documentation",
 *      description="Boom",
 *      @OA\Contact(
 *          email="yaryna.vitaliy@gmail.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_API_V1_CONST_HOST,
 *      description="Boom Api Endpoint"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="sanctum",
 * ),
 */
abstract class BaseController extends Controller
{
}
