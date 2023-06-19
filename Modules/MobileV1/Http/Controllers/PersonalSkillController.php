<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Models\PersonalSkill;
use App\Transformers\PersonalSkillTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class PersonalSkillController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/personal-skills",
     *   tags={"PersonalSkill"},
     *   summary="Get personal skills",
     *   description="Get personal skill list",
     *   operationId="getPersonalSkills",
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
        $personalSkills = PersonalSkill::makeCacheByUniqueRequest(function () {
            return PersonalSkill::all();
        });

        return fractal($personalSkills, new PersonalSkillTransformer())->serializeWith(new ArraySerializer());
    }
}
