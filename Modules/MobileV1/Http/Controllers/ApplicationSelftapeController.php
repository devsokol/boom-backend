<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationSelftapeAttachment;
use App\Models\AttachmentType;
use App\Services\Common\Attachment\AttachmentService;
use Illuminate\Http\Response;
use Modules\ApiV1\Transformers\ApplicationSelftapeTransformer;
use Modules\MobileV1\Actions\ApplicationSelftape\ApplicationSelftapeReject;
use Modules\MobileV1\Actions\ApplicationSelftape\ApplicationSelftapeRelations;
use Modules\MobileV1\Actions\ApplicationSelftape\ApplicationSelftapeRightsCheck;
use Modules\MobileV1\Actions\ApplicationSelftape\ApplicationSelftapeSended;
use Modules\MobileV1\Http\Requests\UploadAttachmentRequest;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class ApplicationSelftapeController extends BaseController
{
    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Get(path="/application-selftapes/{application_id}/detail",
     *   tags={"ApplicationSelftape"},
     *   summary="Get an application selftape detail",
     *   description="Get an application selftape detail",
     *   operationId="getApplicationSelftapeDetail",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *     name="application_id",
     *     required=true,
     *     in="path",
     *     description="id",
     *
     *     @OA\Schema(
     *         type="string"
     *     )
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
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   ),
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function applicationSelftapeDetail(
        Application $application,
        ApplicationSelftapeRelations $action
    ): Fractal {
        $this->rightsCheck($application);

        abort_unless($application->applicationSelftape, Response::HTTP_NOT_FOUND);

        $action->handle($application);

        return fractal(
            $application->applicationSelftape,
            new ApplicationSelftapeTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Get(path="/application-selftapes/{application_id}/sended",
     *   tags={"ApplicationSelftape"},
     *   summary="Change status to sended",
     *   description="Change the status to sended for application selftape by application id",
     *   operationId="changeApplicationSelftapeStatusToSended",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *     name="application_id",
     *     required=true,
     *     in="path",
     *     description="id",
     *
     *     @OA\Schema(
     *         type="string"
     *     )
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
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   ),
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function sended(Application $application, ApplicationSelftapeSended $action): Fractal
    {
        $this->rightsCheck($application);

        abort_unless($application->applicationSelftape, Response::HTTP_NOT_FOUND);

        $action->handle($application);

        return fractal(
            $application->applicationSelftape,
            new ApplicationSelftapeTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @codingStandardsIgnoreStart
     *
     * @OA\Get(path="/application-selftapes/{application_id}/reject",
     *   tags={"ApplicationSelftape"},
     *   summary="Change status to reject",
     *   description="Change the status to rejected for application selftape by application id",
     *   operationId="changeApplicationSelftapeStatusToRejected",
     *   security={ {"bearerAuth": {} }},
     *
     *   @OA\Parameter(
     *     name="application_id",
     *     required=true,
     *     in="path",
     *     description="id",
     *
     *     @OA\Schema(
     *         type="string"
     *     )
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
     *   @OA\Response(
     *       response=404,
     *       description="Not Found"
     *   ),
     * )
     *
     * @codingStandardsIgnoreEnd
     */
    public function reject(Application $application, ApplicationSelftapeReject $action): Fractal
    {
        $this->rightsCheck($application);

        abort_unless($application->applicationSelftape, Response::HTTP_NOT_FOUND);

        $action->handle($application);

        return fractal(
            $application->applicationSelftape,
            new ApplicationSelftapeTransformer()
        )->serializeWith(new ArraySerializer());
    }

    /**
     * @OA\Post(
     *     path="/application-selftapes/{application_id}/provide",
     *     summary="Store a new resource",
     *     operationId="storeAdditionSelftapeForActor",
     *     tags={"ApplicationSelftape"},
     *     security={ {"bearerAuth": {} }},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *                 type="object",
     *
     *                 @OA\Property(
     *                     property="attachments",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(
     *                             property="file",
     *                             type="string",
     *                             format="binary",
     *                             description="Attachment file.",
     *                         ),
     *                         @OA\Property(
     *                             property="link",
     *                             type="string",
     *                             description="Attachment link. Either 'file' or 'link' property is required.",
     *                         ),
     *                         @OA\Property(
     *                             property="type",
     *                             type="string",
     *                             description="Attachment type.",
     *                         ),
     *                         @OA\Property(
     *                             property="description",
     *                             type="string",
     *                             nullable=true,
     *                             description="Attachment description.",
     *                         ),
     *                     ),
     *                 ),
     *                 @OA\Property(
     *                     property="save_to_profile",
     *                     type="boolean",
     *                     nullable=true,
     *                     description="Save to profile flag",
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response="204",
     *         description="Missing content",
     *
     *         @OA\JsonContent(),
     *     ),
     *
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable entity",
     *
     *         @OA\JsonContent(),
     *     ),
     * )
     */
    public function provide(
        Application $application,
        UploadAttachmentRequest $request,
        AttachmentService $attachmentService
    ) {
        $attachments = [];

        if ($request->has('attachments')) {
            foreach ($request->validated()['attachments'] as $attachmentPayload) {
                $actor = null;
                if ($request->has('save_to_profile') && $request->validated()['save_to_profile']) {
                    $actor = $request->user();
                }

                $attachment = $attachmentService->storeAttachment(
                    $attachmentPayload['file'] ?? null,
                    $attachmentPayload['link'] ?? null,
                    $actor,
                    AttachmentType::getType($attachmentPayload['type']),
                    $attachmentPayload['description'] ?? null
                );
                $attachmentService->reset();

                $attachments[] = $attachment;
            }
        }

        foreach ($attachments as $attachment) {
            ApplicationSelftapeAttachment::create([
                'attachment_id' => $attachment->id,
                'application_selftape_id' => $application->applicationSelftape->id,
                'is_actor' => true,
            ]);
        }

        $application->update(['status' => ApplicationStatus::SELFTAPE_PROVIDED]);

        return response()->json([], 204);
    }

    private function rightsCheck(Application $application): void
    {
        app(ApplicationSelftapeRightsCheck::class)->handle($application);
    }
}
