<?php

namespace App\Observers;

use App\Models\Attachment;
use App\Models\AttachmentType;
use App\Services\LocalVideo\LocalVideoService;
use App\Services\UploadMedia\UploadMediaService;
use Illuminate\Support\Facades\Storage;

class AttachmentObserver
{
    public function deleting(Attachment $attachment): void
    {
        $formatFile = AttachmentType::getFormatFile($attachment->mime_type);

        if ($formatFile === 'document' || $formatFile === 'image') {
            $disk = UploadMediaService::defaultDisk();
            $relativePath = parse_url($attachment->attachment_repository)['path'];

            $this->deleteFile($disk, $relativePath);
        } elseif ($formatFile === 'video') {
            $localVideoService = new LocalVideoService();
            $localVideoService->delete($attachment->getAttributes()['attachment_repository']);
        }
    }

    /**
     * Deletes a file from the specified disk if it exists.
     *
     * @param  string  $disk The storage disk to delete the file from.
     * @param  string  $relativePath The relative path of the file on the disk.
     */
    public function deleteFile(string $disk, string $relativePath): void
    {
        if (Storage::disk($disk)->exists($relativePath)) {
            Storage::disk($disk)->delete($relativePath);
        }
    }
}
