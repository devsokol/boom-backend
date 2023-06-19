<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Models\ActorNotification;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\JsonApiSerializer;
use Modules\MobileV1\Actions\ActorNotification\ActorNotificationCheckRights;
use Modules\MobileV1\Actions\ActorNotification\ActorNotificationList;
use Modules\MobileV1\Actions\ActorNotification\ActorNotificationMarkAsRead;
use Modules\MobileV1\Transformers\ActorNotificationTransformer;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class ActorNotificationController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/actor-notifications",
     *   tags={"ActorNotification"},
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
     *       description="Not Found."
     *   )
     * )
     * @codingStandardsIgnoreEnd
     */
    public function index(ActorNotificationList $action): Fractal
    {
        $paginator = $action->handle();

        $notifications = $paginator->getCollection();

        return fractal($notifications, new ActorNotificationTransformer())
            ->withResourceName('actor_notification')
            ->serializeWith(new JsonApiSerializer())
            ->paginateWith(new IlluminatePaginatorAdapter($paginator));
    }

    /**
     * @codingStandardsIgnoreStart
     * @OA\Get(path="/actor-notifications/{notification_id}/mark-as-read",
     *   tags={"ActorNotification"},
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
    public function markAsRead(ActorNotification $notification): Fractal
    {
        (new ActorNotificationCheckRights())->handle($notification);
        (new ActorNotificationMarkAsRead())->handle($notification);

        return fractal($notification, new ActorNotificationTransformer())->serializeWith(new ArraySerializer());
    }
}
