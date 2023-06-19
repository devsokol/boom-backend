<?php

use Illuminate\Routing\Router;
use Modules\ApiV1\Http\Controllers\ApplicationController;
use Modules\ApiV1\Http\Controllers\ApplicationSelftapeController;
use Modules\ApiV1\Http\Controllers\ApplicationSelftapeMaterialController;
use Modules\ApiV1\Http\Controllers\AuditionController;
use Modules\ApiV1\Http\Controllers\AuditionMaterialController;
use Modules\ApiV1\Http\Controllers\AuthController;
use Modules\ApiV1\Http\Controllers\CurrencyController;
use Modules\ApiV1\Http\Controllers\EthnicityController;
use Modules\ApiV1\Http\Controllers\GenreController;
use Modules\ApiV1\Http\Controllers\MaterialTypeController;
use Modules\ApiV1\Http\Controllers\PaymentTypeController;
use Modules\ApiV1\Http\Controllers\PersonalSkillController;
use Modules\ApiV1\Http\Controllers\ProjectController;
use Modules\ApiV1\Http\Controllers\ProjectTypeController;
use Modules\ApiV1\Http\Controllers\RecommendRoleController;
use Modules\ApiV1\Http\Controllers\RoleController;
use Modules\ApiV1\Http\Controllers\RoleMaterialController;
use Modules\ApiV1\Http\Controllers\SettingController;
use Modules\ApiV1\Http\Controllers\UserNotificationController;

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

        //region Unauthorized USER
        $r->prefix('user')->controller(AuthController::class)->group(function (Router $r) {
            $r->post('register', 'register');
            $r->post('login', 'login');
            $r->post('forgot-password', 'forgotPassword');
            $r->post('check-reset-password-code', 'checkResetPasswordCode');
            $r->put('reset-password', 'resetPassword');
        });
        //endregion Unauthorized USER

        $r->middleware('auth:api')->group(function (Router $r) {
            $r->post('user/refresh-access-token', [AuthController::class, 'refreshToken'])
                ->middleware('ability:refresh-token');

            $r->middleware('ability:auth-token')->group(function (Router $r) {
                //region Authorized USER
                $r->prefix('user')->controller(AuthController::class)->group(function (Router $r) {
                    $r->post('me', 'me');
                    $r->put('change-password', 'changePassword');
                    $r->put('upload-avatar', 'uploadAvatar');
                    $r->post('logout', 'logout');
                });
                //endregion Authorized USER

                $r->get('currencies', CurrencyController::class);
                $r->get('genres', GenreController::class);
                $r->get('ethnicities', EthnicityController::class);
                $r->get('payment-types', PaymentTypeController::class);
                $r->get('personal-skills', PersonalSkillController::class);
                $r->get('material-types', MaterialTypeController::class);
                $r->get('project-types', ProjectTypeController::class);

                $r->post('projects/{project}/archive', [ProjectController::class, 'archive']);
                $r->post('projects/{project}/restore', [ProjectController::class, 'restore']);
                $r->get('projects/active-projects-and-roles', [ProjectController::class, 'activeProjectsRoles']);
                $r->apiResource('projects', ProjectController::class);

                //region applications
                $r->get('applications/statuses', [ApplicationController::class, 'statuses']);

                $r->controller(AuditionController::class)->group(function (Router $r) {
                    $r->get('/applications/{application}/audition-request', 'show');
                    $r->post('/applications/{application}/audition-request', 'store');
                    $r->put('/applications/{application}/audition-request', 'update');
                });

                $r->controller(ApplicationSelftapeController::class)->group(function (Router $r) {
                    $r->get('/applications/{application}/selftape-request', 'show');
                    $r->post('/applications/{application}/selftape-request', 'store');
                    $r->put('/applications/{application}/selftape-request', 'update');
                });

                $r->controller(RecommendRoleController::class)->group(function (Router $r) {
                    $r->get('/applications/{application}/recommend-role', 'show');
                    $r->post('/applications/{application}/recommend-role', 'store');
                    $r->put('/applications/{application}/recommend-role', 'update');
                });

                $r->controller(ApplicationController::class)->group(function (Router $r) {
                    $r->get('/applications/{application}/accept', 'acceptApplication');
                    $r->get('/applications/{application}/reject', 'rejectApplication');
                });

                $r->apiResource('roles.applications', ApplicationController::class)->only(['index', 'show']);
                //endregion applications

                //region user notifications

                $r->prefix('user-notifications')
                    ->controller(UserNotificationController::class)->group(function (Router $r) {
                        $r->get('', 'index');
                        $r->get('status-about-new-notifications', 'statusNewNotification');
                        $r->get('{notification}/mark-as-read', 'markAsRead');
                    });
                //endregion user notifications

                //region role
                $r->post(
                    'projects/{project}/roles/{role}/label-color-range',
                    [RoleController::class, 'updateLabelColorRange']
                )->scopeBindings();
                $r->post('roles/{role}/archive', [RoleController::class, 'archive']);
                $r->post('roles/{id}/restore', [RoleController::class, 'restore']);
                $r->get('roles/{role}/renew-dynamic-link', [RoleController::class, 'renewDynamicLink']);
                $r->apiResource('projects.roles', RoleController::class);
                //endregion role

                //region application selftape materials
                $r->prefix('application-selftape-materials')
                    ->controller(ApplicationSelftapeMaterialController::class)->group(function (Router $r) {
                        $r->get('{id}/application-selftape', 'index');
                        $r->get('{id}', 'show');
                        $r->post('{id}/audition', 'store');
                        $r->post('{id}/update', 'update');
                        $r->delete('{id}', 'destroy');
                    });
                //endregion application selftape materials

                //region audition materials
                $r->prefix('audition-materials')
                    ->controller(AuditionMaterialController::class)->group(function (Router $r) {
                        $r->get('{id}/audition', 'index');
                        $r->get('{id}', 'show');
                        $r->post('{id}/audition', 'store');
                        $r->post('{id}/update', 'update');
                        $r->delete('{id}', 'destroy');
                    });
                //endregion audition materials

                //region role materials
                $r->prefix('role-materials')->controller(RoleMaterialController::class)->group(function (Router $r) {
                    $r->get('{id}/role', 'index');
                    $r->get('{id}', 'show');
                    $r->post('{id}/role', 'store');
                    $r->post('{id}/update', 'update');
                    $r->delete('{id}', 'destroy');
                });
                //endregion role materials
            });
        });
    });
