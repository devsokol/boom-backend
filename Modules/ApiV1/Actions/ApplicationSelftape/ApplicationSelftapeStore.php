<?php

namespace Modules\ApiV1\Actions\ApplicationSelftape;

use App\Dto\ApplicationSelftapeData;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationSelftapeAttachment;
use App\Models\AttachmentType;
use App\Services\Common\Attachment\AttachmentService;
use Illuminate\Support\Facades\DB;

class ApplicationSelftapeStore
{
    /**
     * @var AttachmentService
     */
    public function __construct(protected AttachmentService $attachmentService)
    {
    }

    public function handle(
        ApplicationSelftapeData $payload,
        Application $application
    ) {
        $payload = collect($payload->toArray());

        $application->load('applicationSelftape');

        return DB::transaction(function () use ($payload, $application) {
            /*
             * If the application has an associated applicationSelftape,
             * this code will delete all related records in the applicationSelftape table
             * and remove the associated files.
             */
            if ($application->applicationSelftape) {
                $application->applicationSelftape->delete();
            }

            /**
             * Create a new record in the applicationselftape table.
             */
            $applicationSelftape = $application
                ->applicationSelftape()
                ->create($payload->except('materials')->toArray());

            $materials = $payload->get('materials');

            if ($materials) {
                $applicationSelftapeAttachments = [];

                foreach ($materials as $material) {
                    $attachment = $this->attachmentService->storeAttachment(
                        $material['attachment'],
                        null,
                        null,
                        AttachmentType::find($material['material_type_id'])
                    );
                    $this->attachmentService->reset();

                    $applicationSelftapeAttachments[] = [
                        'attachment_id' => $attachment->id,
                        'application_selftape_id' => $applicationSelftape->id,
                    ];
                }

                ApplicationSelftapeAttachment::insert($applicationSelftapeAttachments);
            }

            $applicationSelftape->load(['applicationSelftapeAttachment.attachment.attachmentType']);

            $application->update(['status' => ApplicationStatus::SELFTAPE_REQUEST]);

            return $applicationSelftape;
        });
    }
}
