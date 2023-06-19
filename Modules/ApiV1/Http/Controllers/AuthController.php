<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ApiV1\Http\Requests\ChangePasswordRequest;
use Modules\ApiV1\Http\Requests\CheckResetPasswordCodeRequest;
use Modules\ApiV1\Http\Requests\ForgotPasswordRequest;
use Modules\ApiV1\Http\Requests\LoginUserRequest;
use Modules\ApiV1\Http\Requests\RegisterUserRequest;
use Modules\ApiV1\Http\Requests\ResetPasswordRequest;
use Modules\ApiV1\Http\Requests\UploadAvatarRequest;
use Modules\ApiV1\Services\Support\Sanctum\AuthSanctumService;
use Modules\ApiV1\Services\User\AuthService;
use Modules\ApiV1\Transformers\AuthTransformer;
use Modules\ApiV1\Transformers\UserTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class AuthController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/user/me",
     *   tags={"User"},
     *   summary="Get a user",
     *   description="Get a user info",
     *   operationId="getUser",
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
        $user = $request->user();

        return fractal($user, new UserTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/user/register",
     *   tags={"User"},
     *   summary="Create a new user",
     *   description="Create a new user",
     *   operationId="createNewUser",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"first_name", "last_name", "email", "password", "password_confirmation"},
     *               @OA\Property(property="first_name", type="string", description="User's first name", example="Ivan"),
     *               @OA\Property(property="last_name", type="string", description="User's last name", example="Ivanov"),
     *               @OA\Property(property="company_name", type="string", description="Company address", example="21st Century Fox"),
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
    public function register(RegisterUserRequest $request, AuthSanctumService $service): JsonResponse
    {
        $user = User::create($request->validated());

        $tokens = $service->createPairTokens($user);

        return fractal([$tokens], new AuthTransformer($user))->serializeWith(new ArraySerializer())->respond(201, []);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/user/login",
     *   tags={"User"},
     *   summary="Fetch a bearer token",
     *   description="",
     *   operationId="loginUser",
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
    public function login(LoginUserRequest $request, AuthSanctumService $service): Fractal
    {
        [
            'tokens' => $tokens,
            'user' => $user
        ] = $service->attempt(
            $request->get('email'),
            $request->get('password'),
            (bool) $request->get('remember_me')
        );

        return fractal([$tokens], new AuthTransformer($user))->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/user/refresh-access-token",
     *   tags={"User"},
     *   summary="Fetch a new access & refresh token pair",
     *   description="To get a new token pair: access_tokens & refresh_tokens you need a valid refresh_token",
     *   operationId="userRefreshAccessRefreshToken",
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
        $user = $request->user();

        $tokens = $service->createPairTokens($user, (bool) $request->get('remember_me'));

        return fractal([$tokens], new AuthTransformer($user))->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/user/logout",
     *   tags={"User"},
     *   summary="User logout",
     *   description="User logout",
     *   operationId="userLogout",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Response(
     *     response=204,
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
    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => __('Tokens Revoked'),
        ], 204);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/user/forgot-password",
     *   tags={"User"},
     *   summary="Fetch a token for reset password",
     *   description="",
     *   operationId="forgotPassword",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"email"},
     *               @OA\Property(property="email", type="string", description="User's email", example="admin@gmail.com"),
     *           ),
     *      )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Reset password link sent on your email",
     *       @OA\MediaType(mediaType="application/json")
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Unprocessable Entity"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function forgotPassword(ForgotPasswordRequest $request, AuthService $service): JsonResponse
    {
        $service->forgotPassword($request->get('email'));

        return response()->json(['message' => __('The reset code has been successfully sent.')]);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Post(path="/user/check-reset-password-code",
     *   tags={"User"},
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
     * @OA\Put(path="/user/reset-password",
     *   tags={"User"},
     *   summary="Reset password",
     *   description="Reset password for a user",
     *   operationId="resetPasswordForUser",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               required={"code", "gateway", "receiver", "password_confirmation", "password"},
     *               @OA\Property(property="code", type="string", description="Reset code", example="12345678"),
     *               @OA\Property(property="email", type="string", description="User's email", example="admin@gmail.com"),
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
     * @OA\Put(path="/user/change-password",
     *   tags={"User"},
     *   summary="Change password",
     *   description="Change user's password",
     *   operationId="userChangePassword",
     *   security={ {"bearerAuth": {} }},
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               @OA\Property( property="old_password", type="string", format="password", description="Old password", example="Ss12345678_#"),
     *               @OA\Property(property="password", description="User's password", type="password", example="Ss12345678_#"),
     *               @OA\Property(property="password_confirmation", type="password", description="Confirmation password", example="Ss12345678_#"),
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Password has been successfully changed"
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $request->user()->update($request->only('password'));

        return response()->json(['message' => __('Password has been successfully changed')]);
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Put(path="/user/upload-avatar",
     *   tags={"User"},
     *   summary="upload a profile avatar",
     *   description="Upload a avatar",
     *   operationId="userUploadAvatar",
     *   security={ {"bearerAuth": {} }},
     *   @OA\RequestBody(
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              @OA\Property(property="avatar", type="string", description="project image", example="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAApgAAAKYB3X3/OAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAANCSURBVEiJtZZPbBtFFMZ/M7ubXdtdb1xSFyeilBapySVU8h8OoFaooFSqiihIVIpQBKci6KEg9Q6H9kovIHoCIVQJJCKE1ENFjnAgcaSGC6rEnxBwA04Tx43t2FnvDAfjkNibxgHxnWb2e/u992bee7tCa00YFsffekFY+nUzFtjW0LrvjRXrCDIAaPLlW0nHL0SsZtVoaF98mLrx3pdhOqLtYPHChahZcYYO7KvPFxvRl5XPp1sN3adWiD1ZAqD6XYK1b/dvE5IWryTt2udLFedwc1+9kLp+vbbpoDh+6TklxBeAi9TL0taeWpdmZzQDry0AcO+jQ12RyohqqoYoo8RDwJrU+qXkjWtfi8Xxt58BdQuwQs9qC/afLwCw8tnQbqYAPsgxE1S6F3EAIXux2oQFKm0ihMsOF71dHYx+f3NND68ghCu1YIoePPQN1pGRABkJ6Bus96CutRZMydTl+TvuiRW1m3n0eDl0vRPcEysqdXn+jsQPsrHMquGeXEaY4Yk4wxWcY5V/9scqOMOVUFthatyTy8QyqwZ+kDURKoMWxNKr2EeqVKcTNOajqKoBgOE28U4tdQl5p5bwCw7BWquaZSzAPlwjlithJtp3pTImSqQRrb2Z8PHGigD4RZuNX6JYj6wj7O4TFLbCO/Mn/m8R+h6rYSUb3ekokRY6f/YukArN979jcW+V/S8g0eT/N3VN3kTqWbQ428m9/8k0P/1aIhF36PccEl6EhOcAUCrXKZXXWS3XKd2vc/TRBG9O5ELC17MmWubD2nKhUKZa26Ba2+D3P+4/MNCFwg59oWVeYhkzgN/JDR8deKBoD7Y+ljEjGZ0sosXVTvbc6RHirr2reNy1OXd6pJsQ+gqjk8VWFYmHrwBzW/n+uMPFiRwHB2I7ih8ciHFxIkd/3Omk5tCDV1t+2nNu5sxxpDFNx+huNhVT3/zMDz8usXC3ddaHBj1GHj/As08fwTS7Kt1HBTmyN29vdwAw+/wbwLVOJ3uAD1wi/dUH7Qei66PfyuRj4Ik9is+hglfbkbfR3cnZm7chlUWLdwmprtCohX4HUtlOcQjLYCu+fzGJH2QRKvP3UNz8bWk1qMxjGTOMThZ3kvgLI5AzFfo379UAAAAASUVORK5CYII="),
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
     *       description="The given data was invalid."
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function uploadAvatar(UploadAvatarRequest $request): Fractal
    {
        $user = $request->user();

        $user->update($request->validated());

        return fractal($user, new UserTransformer())->serializeWith(new ArraySerializer());
    }
}
