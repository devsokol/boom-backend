<?php

namespace App\Observers;

use App\Models\ApplicationSelftape;

class ApplicationSelftapeObserver
{
    public function deleting(ApplicationSelftape $applicationSelftape): void
    {
        $applicationSelftape->load('applicationSelftapeAttachment.attachment');

        $applicationSelftape->applicationSelftapeAttachment->each(function ($applicationSelftapeAttachmentItem) {
            $applicationSelftapeAttachmentItem->delete();
            $applicationSelftapeAttachmentItem->attachment->delete();
        });
    }
}
