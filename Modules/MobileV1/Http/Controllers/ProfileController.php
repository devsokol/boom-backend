<?php

namespace Modules\MobileV1\Http\Controllers;

use Modules\MobileV1\Actions\Actor\ActorLoadRelations;
use Modules\MobileV1\Actions\Actor\ActorUpdateSetting;
use Modules\MobileV1\Http\Requests\ActorSettingRequest;
use Modules\MobileV1\Http\Requests\ProfileRequest;
use Modules\MobileV1\Services\Actor\ProfileService;
use Modules\MobileV1\Transformers\ActorTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class ProfileController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Post(path="/actor/me/update",
     *   tags={"Actor"},
     *   summary="Update an actor",
     *   description="Update an actor",
     *   operationId="updateActor",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\RequestBody(
     *
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *
     *           @OA\Schema(
     *               required={"first_name", "last_name"},
     *
     *               @OA\Property(property="first_name", type="string", description="first name", example="Ivan"),
     *               @OA\Property(property="last_name", type="string", description="last name", example="Ivanov"),
     *               @OA\Property(property="bio", type="string", description="bio", example="Some text will be here..."),
     *               @OA\Property(property="city", type="string", description="city", example="Kyiv"),
     *               @OA\Property( property="skill_list[]", description="skill list", type="integer", example="1"),
     *               @OA\Property(property="ethnicity_id", type="integer", description="ethnicity id", example="1"),
     *               @OA\Property(
     *                   property="acting_gender[]",
     *                   description="Acting gender: Male - 0; Female - 1; Other - 2;",
     *               ),
     *               @OA\Property(property="min_age", type="string", description="min age", example="1"),
     *               @OA\Property(property="max_age", type="string", description="max age", example="100"),
     *               @OA\Property(property="pseudonym", type="string", description="pseudonym", example="Vortex"),
     *               @OA\Property(property="behance_link", type="string", description="behance link", example="https://www.behance.net/vortex"),
     *               @OA\Property(property="instagram_link", type="string", description="instagram link", example="https://www.instagram.com/natgeo/"),
     *               @OA\Property(property="youtube_link", type="string", description="instagram link", example="https://www.youtube.com/channel/UCpVm7bg6pXKo1Pr6k5kxG9A"),
     *               @OA\Property(property="facebook_link", type="string", description="instagram link", example="https://www.facebook.com/natgeo"),
     *               @OA\Property(property="headshots[]", type="string", description="headshot", example="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAApgAAAKYB3X3/OAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAANCSURBVEiJtZZPbBtFFMZ/M7ubXdtdb1xSFyeilBapySVU8h8OoFaooFSqiihIVIpQBKci6KEg9Q6H9kovIHoCIVQJJCKE1ENFjnAgcaSGC6rEnxBwA04Tx43t2FnvDAfjkNibxgHxnWb2e/u992bee7tCa00YFsffekFY+nUzFtjW0LrvjRXrCDIAaPLlW0nHL0SsZtVoaF98mLrx3pdhOqLtYPHChahZcYYO7KvPFxvRl5XPp1sN3adWiD1ZAqD6XYK1b/dvE5IWryTt2udLFedwc1+9kLp+vbbpoDh+6TklxBeAi9TL0taeWpdmZzQDry0AcO+jQ12RyohqqoYoo8RDwJrU+qXkjWtfi8Xxt58BdQuwQs9qC/afLwCw8tnQbqYAPsgxE1S6F3EAIXux2oQFKm0ihMsOF71dHYx+f3NND68ghCu1YIoePPQN1pGRABkJ6Bus96CutRZMydTl+TvuiRW1m3n0eDl0vRPcEysqdXn+jsQPsrHMquGeXEaY4Yk4wxWcY5V/9scqOMOVUFthatyTy8QyqwZ+kDURKoMWxNKr2EeqVKcTNOajqKoBgOE28U4tdQl5p5bwCw7BWquaZSzAPlwjlithJtp3pTImSqQRrb2Z8PHGigD4RZuNX6JYj6wj7O4TFLbCO/Mn/m8R+h6rYSUb3ekokRY6f/YukArN979jcW+V/S8g0eT/N3VN3kTqWbQ428m9/8k0P/1aIhF36PccEl6EhOcAUCrXKZXXWS3XKd2vc/TRBG9O5ELC17MmWubD2nKhUKZa26Ba2+D3P+4/MNCFwg59oWVeYhkzgN/JDR8deKBoD7Y+ljEjGZ0sosXVTvbc6RHirr2reNy1OXd6pJsQ+gqjk8VWFYmHrwBzW/n+uMPFiRwHB2I7ih8ciHFxIkd/3Omk5tCDV1t+2nNu5sxxpDFNx+huNhVT3/zMDz8usXC3ddaHBj1GHj/As08fwTS7Kt1HBTmyN29vdwAw+/wbwLVOJ3uAD1wi/dUH7Qei66PfyuRj4Ik9is+hglfbkbfR3cnZm7chlUWLdwmprtCohX4HUtlOcQjLYCu+fzGJH2QRKvP3UNz8bWk1qMxjGTOMThZ3kvgLI5AzFfo379UAAAAASUVORK5CYII="),
     *               @OA\Property(
     *                   description="Selftape",
     *                   property="selftapes[]",
     *                   type="array",
     *
     *                   @OA\Items(type="string", format="binary")
     *               )
     *           )
     *       )
     *   ),
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
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function update(ProfileRequest $request, ProfileService $service): Fractal
    {
        $actor = $service->update($request->validated());

        (new ActorLoadRelations())->handle($actor);

        return fractal($actor, new ActorTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Patch(path="/actor/me/settings",
     *   tags={"Actor"},
     *   summary="Update actor settings",
     *   description="Update actor settings",
     *   operationId="updateActorSettings",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\RequestBody(
     *
     *       @OA\MediaType(
     *           mediaType="application/json",
     *
     *           @OA\Schema(
     *
     *               @OA\Property(property="allow_app_notification", type="boolean", example="true"),
     *               @OA\Property(property="role_approve_notification", type="boolean", example="true"),
     *               @OA\Property(property="role_reject_notification", type="boolean", example="true"),
     *               @OA\Property(property="role_offer_notification", type="boolean", example="true"),
     *               @OA\Property(property="audition_notification", type="boolean", example="true"),
     *               @OA\Property(property="selftape_notification", type="boolean", example="true"),
     *           )
     *       )
     *   ),
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
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function settingUpdate(ActorSettingRequest $request): Fractal
    {
        $actor = (new ActorUpdateSetting())->handle($request->validated());

        (new ActorLoadRelations())->handle($actor);

        return fractal($actor, new ActorTransformer())->serializeWith(new ArraySerializer());
    }
}
