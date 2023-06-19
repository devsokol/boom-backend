<?php

use Illuminate\Routing\Router;
use Modules\MobileV1\Http\Controllers\ActorController;
use Modules\MobileV1\Http\Controllers\ActorNotificationController;
use Modules\MobileV1\Http\Controllers\ApplicationController;
use Modules\MobileV1\Http\Controllers\ApplicationSelftapeController;
use Modules\MobileV1\Http\Controllers\AttachmentController;
use Modules\MobileV1\Http\Controllers\AuditionController;
use Modules\MobileV1\Http\Controllers\AuthController;
use Modules\MobileV1\Http\Controllers\CurrencyController;
use Modules\MobileV1\Http\Controllers\EthnicityController;
use Modules\MobileV1\Http\Controllers\GenreController;
use Modules\MobileV1\Http\Controllers\HeadshotController;
use Modules\MobileV1\Http\Controllers\MaterialTypeController;
use Modules\MobileV1\Http\Controllers\PaymentTypeController;
use Modules\MobileV1\Http\Controllers\PersonalSkillController;
use Modules\MobileV1\Http\Controllers\ProfileController;
use Modules\MobileV1\Http\Controllers\ProjectTypeController;
use Modules\MobileV1\Http\Controllers\RecommendRoleController;
use Modules\MobileV1\Http\Controllers\RoleController;
use Modules\MobileV1\Http\Controllers\SelftapeController;
use Modules\MobileV1\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')
    ->as('v1.')
    ->group(function (Router $r) {
        $r->get('settings', SettingController::class);

        //region Unauthorized ACTOR
        $r->prefix('actor')->controller(AuthController::class)->group(function (Router $r) {
            $r->post('register', 'register');
            $r->post('login', 'login');
            $r->post('forgot-password', 'forgotPassword');
            $r->post('check-reset-password-code', 'checkResetPasswordCode');
            $r->put('reset-password', 'resetPassword');
        });
        //endregion Unauthorized ACTOR

        $r->middleware('auth:mobile')->group(function (Router $r) {
            $r->post('actor/refresh-access-token', [AuthController::class, 'refreshToken'])
                ->middleware('ability:refresh-token');

            $r->middleware('ability:auth-token')->group(function (Router $r) {
                //region Authorized ACTOR
                $r->prefix('actor')->controller(AuthController::class)->group(function (Router $r) {
                    $r->post('me', 'me');
                    $r->post('fcm', 'fcmUpdate');
                    $r->get('badge', 'badge');
                    $r->post('resend-verification-code', 'resendVerificationCode');
                    $r->post('verification', 'verification');
                    $r->post('logout', 'logout');
                });
                //endregion Authorized ACTOR

                $r->get('currencies', CurrencyController::class);
                $r->get('genres', GenreController::class);
                $r->get('ethnicities', EthnicityController::class);
                $r->get('payment-types', PaymentTypeController::class);
                $r->get('personal-skills', PersonalSkillController::class);
                $r->get('material-types', MaterialTypeController::class);
                $r->get('project-types', ProjectTypeController::class);

                $r->middleware('verified_user')->group(function (Router $r) {
                    //region ACTOR
                    $r->prefix('actor')->group(function (Router $r) {
                        $r->post('me/update', [ProfileController::class, 'update']);
                        $r->patch('me/settings', [ProfileController::class, 'settingUpdate']);

                        // TODO need delete
                        $r->apiResource('headshots', HeadshotController::class)->only(['store', 'destroy']);
                        $r->apiResource('selftapes', SelftapeController::class)->only(['store', 'destroy']);

                        $r->apiResource('attachments', AttachmentController::class)->only(['store', 'destroy']);
                    });

                    $r->delete('actor/me', [ActorController::class, 'destroy']);
                    //endregion ACTOR

                    $r->get('actor-notifications', [ActorNotificationController::class, 'index']);
                    $r->get(
                        'actor-notifications/{notification}/mark-as-read',
                        [ActorNotificationController::class, 'markAsRead']
                    );

                    //region roles
                    $r->get('roles/filter-tools', [RoleController::class, 'filterTools']);
                    $r->get('roles/cities', [RoleController::class, 'cities']);

                    $r->get('roles/bookmarks', [RoleController::class, 'bookmarks']);
                    $r->get('roles/bookmarks/role/{id}/add', [RoleController::class, 'addBookmark']);
                    $r->get('roles/bookmarks/role/{id}/remove', [RoleController::class, 'removeBookmark']);

                    $r->post('roles/min-max-rate', [RoleController::class, 'minMaxRate']);
                    $r->apiResource('roles', RoleController::class)->only(['index', 'show']);
                    //endregion roles

                    //region application selftape
                    $r->prefix('recommend-roles')
                        ->controller(RecommendRoleController::class)->group(function (Router $r) {
                            $r->get('{application}/detail', 'recommendRoleDetails');
                            $r->get('{application}/accept', 'accept');
                            $r->get('{application}/reject', 'reject');
                        });
                    //endregion application selftape

                    $r->controller(ApplicationController::class)->group(function (Router $r) {
                        $r->get('/applications/{application}/accept-approval', 'acceptApproval');
                        $r->post('/applications/{application}/reject-approval', 'rejectApproval');
                    });

                    //region application selftape
                    $r->prefix('application-selftapes')
                        ->controller(ApplicationSelftapeController::class)->group(function (Router $r) {
                            $r->get('{application}/detail', 'applicationSelftapeDetail');
                            $r->get('{application}/sended', 'sended');
                            $r->get('{application}/reject', 'reject');

                            $r->post('{application}/provide', 'provide');
                        });
                    //endregion application selftape

                    //region auditions
                    $r->prefix('auditions')->controller(AuditionController::class)->group(function (Router $r) {
                        $r->get('{application}/detail', 'auditionDetails');
                        $r->get('{application}/accept', 'accept');
                        $r->post('{application}/reject', 'reject');
                        $r->post('{application}/direct-message', 'directMessage');
                    });
                    //endregion auditions

                    //region application
                    $r->prefix('applications')->controller(ApplicationController::class)->group(function (Router $r) {
                        $r->get('', 'index');
                        $r->get('{application}/show', 'show');
                        $r->get('role/{role}/apply', 'applyForRole');
                    });
                    //endregion application
                });
            });
        });
    });
