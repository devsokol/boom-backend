<?php

namespace Modules\ApiV1\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use League\Fractal\Serializer\JsonApiSerializer;
use Modules\ApiV1\Actions\UserNotification\UserNotificationCheckRights;
use Modules\ApiV1\Actions\UserNotification\UserNotificationIsUnreadExists;
use Modules\ApiV1\Actions\UserNotification\UserNotificationList;
use Modules\ApiV1\Actions\UserNotification\UserNotificationMarkAsRead;
use Modules\ApiV1\Transformers\UserNotificationTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class UserNotificationController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/user-notifications",
     *   tags={"UserNotification"},
     *   summary="Fetch notifications",
     *   description="Fetch notifications",
     *   operationId="fetchNotifications",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *       content={@OA\MediaType(mediaType="application/json",),}
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function index(): Fractal
    {
        $notifications = (new UserNotificationList())->handle();

        return fractal($notifications, new UserNotificationTransformer())
            ->withResourceName('user_notification')
            ->serializeWith(new JsonApiSerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/user-notifications/{notification_id}/mark-as-read",
     *   tags={"UserNotification"},
     *   summary="Change notification status",
     *   description="Change notification status as read",
     *   operationId="changeNotificationStatusAsRead",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Parameter(
     *       name="notification_id",
     *       required=true,
     *       in="path",
     *       description="Notification ID",
     *       @OA\Schema(
     *           type="integer"
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *       content={@OA\MediaType(mediaType="application/json",),}
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function markAsRead(UserNotification $notification): Fractal
    {
        (new UserNotificationCheckRights())->handle($notification);
        (new UserNotificationMarkAsRead())->handle($notification);

        return fractal($notification, new UserNotificationTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/user-notifications/status-about-new-notifications",
     *   tags={"UserNotification"},
     *   summary="Status about new notifications",
     *   description="Status about new notifications",
     *   operationId="statusAboutNewNotifications",
     *   security={ {"bearerAuth": {} }},
     *   @OA\Response(
     *       response=200,
     *       description="Success",
     *       content={@OA\MediaType(mediaType="application/json",),}
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Unauthenticated."
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function statusNewNotification(): JsonResponse
    {
        $isUnreadNotificationExists = (new UserNotificationIsUnreadExists())->handle();

        return response()->json(['new_notification' => $isUnreadNotificationExists]);
    }
}
