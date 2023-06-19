<?php

namespace Modules\MobileV1\Http\Controllers;

use App\Models\ActorAttachments;
use App\Models\Attachment;
use App\Models\AttachmentType;
use App\Services\Common\Attachment\AttachmentService;
use App\Transformers\AttachmentsTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Modules\MobileV1\Http\Requests\UploadAttachmentRequest;
use Spatie\Fractal\Fractal;
use Spatie\Fractalistic\ArraySerializer;

class AttachmentController extends BaseController
{
    /**
     * Store a new resource.
     *
     * @OA\Post(
     *     path="/actor/attachments",
     *     summary="Store a new resource",
     *     operationId="storeResource",
     *     tags={"Attachments"},
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
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Resource successfully stored",
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
    public function store(UploadAttachmentRequest $request, AttachmentService $attachmentService): Fractal
    {
        $actor = $request->user();
        $attachments = [];

        if ($request->has('attachments')) {
            foreach ($request->validated()['attachments'] as $attachmentPayload) {
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

        return fractal($attachments, new AttachmentsTransformer())->serializeWith(new ArraySerializer());
    }

    /**
     * @OA\Delete(
     *     path="/actor/attachments/{attachment}",
     *     tags={"Attachments"},
     *     summary="Delete attachment",
     *     description="Deletes an attachment for the authenticated user",
     *     security={{ "BearerAuth": {} }},
     *
     *     @OA\Parameter(
     *         name="attachment",
     *         in="path",
     *         description="Attachment ID",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="204",
     *         description="No content"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Unauthorized"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Forbidden"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="404",
     *         description="Not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Attachment not found"
     *             )
     *         )
     *     )
     * )
     */
    public function destroy(Attachment $attachment): JsonResponse
    {
        $actorAttachment = ActorAttachments::where('attachment_id', $attachment->id)->firstOrFail();

        Gate::allowIf(fn ($user) => $user->getKey() === $actorAttachment->actor_id);

        $actorAttachment->delete();
        $attachment->delete();

        return response()->json([], 204);
    }

    /**
     * Check the file type and return a string indicating the file type ('video' or 'image').
     *
     * @param  \Illuminate\Http\UploadedFile  $file   The file object that was uploaded from the form
     * @return string                                  A string indicating the file type ('video' or 'image')
     *
     * @throws \Exception                              If the file type is not a video or an image
     */
    public function getFileType(UploadedFile $file): string
    {
        $fileType = '';

        if (strpos($file->getClientMimeType(), 'video') !== false) {
            $fileType = 'video';
        } elseif (strpos($file->getClientMimeType(), 'image') !== false) {
            $fileType = 'image';
        } else {
            throw new \Exception('Incorrect file type');
        }

        return $fileType;
    }
}
