<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Http\Requests\VerificationCodeRequest;
use App\Models\Actor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\MobileV1\Actions\Actor\ActorLoadRelations;
use Modules\MobileV1\Actions\Actor\ActorLogout;
use Modules\MobileV1\Actions\ActorNotification\ActorNotificationBadge;
use Modules\MobileV1\Events\ActorRegisteredEvent;
use Modules\MobileV1\Http\Requests\CheckResetPasswordCodeRequest;
use Modules\MobileV1\Http\Requests\ForgotPasswordRequest;
use Modules\MobileV1\Http\Requests\LoginActorRequest;
use Modules\MobileV1\Http\Requests\RegisterActorRequest;
use Modules\MobileV1\Http\Requests\ResetPasswordRequest;
use Modules\MobileV1\Http\Requests\UpdateFCMTokenRequest;
use Modules\MobileV1\Services\Actor\AuthService;
use Modules\MobileV1\Services\Support\Sanctum\AuthSanctumService;
use Modules\MobileV1\Transformers\ActorTransformer;
use Modules\MobileV1\Transformers\AuthTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class AuthController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/actor/me",
     *   tags={"Actor"},
     *   summary="Get an actor",
     *   description="Get an actor info",
     *   operationId="getActor",
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
     * )
     * @codingStandardsIgnoreEnd
     */
    public function me(Request $request): Fractal
    {
        $actor = $request->user();

        (new ActorLoadRelations())->handle($actor);

        return fractal($actor, new ActorTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\get(path="/actor/badge",
     *   tags={"Actor"},
     *   summary="Get badge count notifications",
     *   description="Get badge count notifications",
     *   operationId="getBadgeCount",
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
     * )
     * @codingStandardsIgnoreEnd
     */
    public function badge(): JsonResponse
    {
        $badgeCount = (new ActorNotificationBadge())->handle(auth()->user());

        return response()->json([
            'badge' => $badgeCount,
        ]);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/actor/resend-verification-code",
     *   tags={"Actor"},
     *   summary="Resend verification code",
     *   description="Resend a verification code if for some reasons user  did not receive a code",
     *   operationId="resendVerificationCode",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=429,
     *       description="Too Many Requests"
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Unsupported parameter gateway"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function resendVerificationCode(Request $request, AuthService $service): JsonResponse
    {
        $actor = $request->user();

        (new ActorLoadRelations())->handle($actor);

        $service->resendVerificationCode($actor);

        return response()->json([
            'message' => __('New code has been sent'),
        ], 201);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/actor/verification",
     *   tags={"Actor"},
     *   summary="Actor verification",
     *   description="Actor verification with code. If verification, successful actor get full access.",
     *   operationId="createVerification",
     *   security={ {"bearerAuth": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"code"},
     *               @OA\Property(property="code", type="string", description="Verification code", example="1234"),
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Success",
     *     @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Unprocessable Entity"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function verification(VerificationCodeRequest $request): Fractal
    {
        $actor = $request->user();

        (new ActorLoadRelations())->handle($actor);

        $actor->afterSuccessfulVerification(function ($actor) {
            return $actor->update(['is_account_verified' => true]);
        });

        return fractal($actor, new ActorTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/actor/register",
     *   tags={"Actor"},
     *   summary="Create a new actor",
     *   description="Create a new actor",
     *   operationId="createNewActor",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"first_name", "last_name", "email", "password", "password_confirmation"},
     *               @OA\Property(property="first_name", type="string", description="User's first name", example="Ivan"),
     *               @OA\Property(property="last_name", type="string", description="User's last name", example="Ivanov"),
     *               @OA\Property(property="phone_number", type="string", description="Phone number", example="+4731005403"),
     *               @OA\Property(property="email", type="string", description="User's email", example="admin@gmail.com"),
     *               @OA\Property(property="password", description="User's password", type="password", example="Ss12345678_#"),
     *               @OA\Property(property="password_confirmation", type="password", description="Confirmation password", example="Ss12345678_#"),
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Success",
     *     @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Unprocessable Entity"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function register(RegisterActorRequest $request, AuthSanctumService $service): JsonResponse
    {
        $actor = Actor::create($request->validated());

        (new ActorLoadRelations())->handle($actor);

        $tokens = $service->createPairTokens($actor);

        event(new ActorRegisteredEvent($actor));

        return fractal([$tokens], new AuthTransformer($actor))->serializeWith(new ArraySerializer())->respond(201, []);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/actor/login",
     *   tags={"Actor"},
     *   summary="Fetch a bearer token",
     *   description="",
     *   operationId="loginActor",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="string", description="User's email", example="admin@gmail.com"),
     *               @OA\Property( property="password", description="User's password.", type="password", format="password", example="Ss12345678_#"),
     *               @OA\Property( property="remember_me", description="Increase tokens lifetime", example="false")
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Successfully",
     *       @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Missing user or incorrect email / password"
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Unprocessable Entity"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function login(LoginActorRequest $request, AuthSanctumService $service): Fractal
    {
        [
            'tokens' => $tokens,
            'user' => $actor
        ] = $service->attempt(
            $request->get('email'),
            $request->get('password'),
            (bool) $request->get('remember_me')
        );

        (new ActorLoadRelations())->handle($actor);

        return fractal([$tokens], new AuthTransformer($actor))->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/actor/refresh-access-token",
     *   tags={"Actor"},
     *   summary="Fetch a new access & refresh token pair",
     *   description="To get a new token pair: access_tokens & refresh_tokens you need a valid refresh_token",
     *   operationId="actorRefreshAccessRefreshToken",
     *   security={ {"bearerAuth": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               @OA\Property( property="remember_me", description="Increase tokens lifetime", example="false")
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Successfully",
     *       @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="Forbidden"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function refreshToken(Request $request, AuthSanctumService $service): Fractal
    {
        $actor = $request->user();

        $tokens = $service->createPairTokens($actor, (bool) $request->get('remember_me'));

        (new ActorLoadRelations())->handle($actor);

        return fractal([$tokens], new AuthTransformer($actor))->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/actor/logout",
     *   tags={"Actor"},
     *   summary="Actor logout",
     *   description="Actor logout",
     *   operationId="actorLogout",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Response(
     *     response=204,
     *     description="Success",
     *     @OA\JsonContent()
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function logout(ActorLogout $action): JsonResponse
    {
        $action->handle(auth()->user());

        return response()->json([
            'message' => __('Tokens Revoked'),
        ], 204);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/actor/forgot-password",
     *   tags={"Actor"},
     *   summary="Fetch a token for reset password",
     *   description="",
     *   operationId="forgotPassword",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"gateway", "receiver"},
     *               @OA\Property(property="gateway", type="string", description="Gateway", example="sms or email"),
     *               @OA\Property(property="receiver", type="string", description="User's email or phone number", example="admin@gmail.com or +4731005403"),
     *           ),
     *      )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Successful",
     *       @OA\MediaType(mediaType="application/json")
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description=""
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function forgotPassword(ForgotPasswordRequest $request, AuthService $service): JsonResponse
    {
        $service->forgotPassword($request->get('gateway'), $request->get('receiver'));

        return response()->json(['message' => __('The reset code has been successfully sent.')]);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/actor/check-reset-password-code",
     *   tags={"Actor"},
     *   summary="Check code",
     *   description="If the code is valid, the user can proceed to the next step",
     *   operationId="checkResetPasswordCode",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"code"},
     *               @OA\Property(property="code", type="string", description="Reset code", example="12345678"),
     *           ),
     *      )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Successful",
     *       @OA\MediaType(mediaType="application/json")
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="This action is unauthorized."
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="\"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function checkResetPasswordCode(CheckResetPasswordCodeRequest $request): JsonResponse
    {
        return response()->json(['message' => __('The reset code :value is valid.', [
            'value' => $request->get('code'),
        ])]);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Put(path="/actor/reset-password",
     *   tags={"Actor"},
     *   summary="Reset password",
     *   description="Reset password for an actor",
     *   operationId="resetPasswordForActor",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"code", "gateway", "receiver", "password_confirmation", "password"},
     *               @OA\Property(property="code", type="string", description="Reset code", example="12345678"),
     *               @OA\Property(property="gateway", type="string", description="Gateway", example="sms or email"),
     *               @OA\Property(property="receiver", type="string", description="User's email or phone number", example="admin@gmail.com or +4731005403"),
     *               @OA\Property(property="password", type="password", description="Password", example="Ss12345678_#"),
     *               @OA\Property(property="password_confirmation", type="password", description="Confirmation password", example="Ss12345678_#"),
     *           ),
     *      )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Successful",
     *       @OA\MediaType(mediaType="application/json")
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description=""
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function resetPassword(ResetPasswordRequest $request, AuthService $service): JsonResponse
    {
        $service->resetPassword($request->get('code'), $request->get('password'));

        return response()->json(['message' => __('Password has been successfully changed.')]);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\post(path="/actor/fcm",
     *   tags={"Actor"},
     *   summary="Update Actor FCM token",
     *   description="Update FCM token for actor",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *          mediaType="multipart/form-data",
     *           @OA\Schema(
     *               required={"fcm_token"},
     *               @OA\Property(property="fcm_token", type="string", description="FCM token"),
     *           ),
     *      )
     *   ),
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
     * )
     * @codingStandardsIgnoreEnd
     */
    public function fcmUpdate(UpdateFCMTokenRequest $request, AuthService $service): JsonResponse
    {
        $service->updateFCM($request->get('fcm_token'));

        return response()->json(['message' => __('Successful!')]);
    }
}
