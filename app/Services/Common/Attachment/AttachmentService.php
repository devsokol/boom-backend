<?php

namespace App\Services\Common\Attachment;

use App\Helpers\StorageHelper;
use App\Models\ActorAttachments;
use App\Models\Attachment;
use App\Models\AttachmentType;
use App\Services\UploadMedia\Drivers\UploadLocalVideoDriver;
use App\Utils\ImageUtility;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttachmentService
{
    /**
     * @const string
     */
    const IMAGE_EXTENSION = 'jpeg';

    private ?UploadedFile $file = null;

    private ?string $link = null;

    private Model|Authenticatable|null $actor;

    private array $payload = [];

    /**
     * Store an attachment.
     *
     * @param  UploadedFile  $file The uploaded file to store.
     * @param  Model|Authenticatable|null  $actor The user or model associated with the attachment.
     * @param  AttachmentType  $attachmentType The type of attachment (e.g. image, video, document).
     * @param  string|null  $description An optional description for the attachment.
     * @return Attachment The stored attachment.
     */
    public function storeAttachment(
        ?UploadedFile $file,
        ?string $link,
        Model|Authenticatable|null $actor,
        AttachmentType $attachmentType,
        string $description = null
    ): Attachment {
        /*
         * Initialize class parameters
         */
        $this->file = $file;
        $this->link = $link;
        $this->actor = $actor;
        $this->payload = [
            'description' => $description,
            'attachment_type_id' => $attachmentType->id,
        ];

        $map = [
            'image' => 'storeImage',
            'video' => 'storeVideo',
            'document' => 'storeFile',
            'link' => 'storeLink',
        ];

        $fileType = is_null($this->link)
            ? $this->getFileType($this->file)
            : 'link';

        return call_user_func([$this, $map[$fileType]]);
    }

    /**
     * Store a new attachment image.
     *
     * @throws \Exception If there is an error during file storage or attachment creation.
     */
    public function storeImage(): Attachment
    {
        try {
            $uniqueName = Str::random(40) . '.' . self::IMAGE_EXTENSION;

            $imageUtility = (new ImageUtility())->compressSavedImage($this->file);
            $path = $imageUtility->storeAs(
                StorageHelper::definePathToSaveContent(new Attachment()),
                $uniqueName
            );

            return $this->addAttachment((object) [
                'name' => $this->getFileName($path),
                'mime_type' => self::IMAGE_EXTENSION,
                'attachment_repository' => $path,
                ...$this->payload,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Store a new attachment video.
     *
     * @throws \Exception If there is an error during file storage or attachment creation.
     */
    public function storeVideo(): Attachment
    {
        try {
            $uploadLocalVideoDriver = new UploadLocalVideoDriver();

            $path = $uploadLocalVideoDriver->store($this->file, new Attachment(), []);

            if (is_null($path) || strlen($path) === 0) {
                throw new \Exception('An error occurred while saving the file');
            }

            $pathToVideo = explode('~', $path)[0];
            $fileName = $this->getFileName($pathToVideo);
            $extension = $this->getExtension($pathToVideo);

            return $this->addAttachment((object) [
                'name' => $fileName,
                'mime_type' => $extension,
                'attachment_repository' => $path,
                ...$this->payload,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Store a new attachment file.
     *
     * @throws \Exception If there is an error during file storage or attachment creation.
     */
    public function storeFile(): Attachment
    {
        try {
            $extension = strtolower($this->file->getClientOriginalExtension());
            $originalName = $this->generateRandomName($extension);
            $definedPath = StorageHelper::definePathToSaveContent(new Attachment());

            /**
             * Check unique original name.
             */
            $originalName = StorageHelper::preventDuplicate($definedPath . DIRECTORY_SEPARATOR . $originalName, true);

            $path = $this->file->storeAs($definedPath, $originalName);

            return $this->addAttachment((object) [
                'name' => $originalName,
                'mime_type' => $extension,
                'attachment_repository' => $path,
                ...$this->payload,
            ]);
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Store a new attachment link.
     */
    public function storeLink(): Attachment
    {
        return $this->addAttachment((object) [
            'attachment_repository' => $this->link,
            ...$this->payload,
        ]);
    }

    /**
     * Store a new attachment in the database.
     *
     * @param  object  $data  The attachment data to store.
     * @return Attachment  The newly created Attachment model.
     *
     * @throws \Exception  If there is an error creating the attachment.
     */
    public function addAttachment(object $data): Attachment
    {
        return DB::transaction(function () use ($data) {
            try {
                $attachment = Attachment::create([
                    'name' => $data->name ?? null,
                    'description' => $data->description ?? null,
                    'mime_type' => $data->mime_type ?? null,
                    'attachment_repository' => $data->attachment_repository,
                    'attachment_type_id' => $data->attachment_type_id,
                ]);

                if ($this->actor) {
                    ActorAttachments::create([
                        'actor_id' => $this->actor->id,
                        'attachment_id' => $attachment->id,
                    ]);
                }

                return $attachment;
            } catch (\Exception $e) {
                throw $e;
            }
        });
    }

    /**
     * @param string
     * @return string
     */
    public function getExtension(string $value): ?string
    {
        $temp = explode('.', $value);

        return end($temp);
    }

    /**
     * @param string
     * @return string
     */
    public function getFileName(string $value): ?string
    {
        $temp = explode('/', $value);

        return trim(
            end($temp),
            '.' . $this->getExtension($value)
        );
    }

    /**
     * Generate a random file name with the given extension.
     *
     * @param  string  $extension The file extension to append to the generated file name.
     * @return string The randomly generated file name with the given extension.
     */
    private function generateRandomName(string $extension): string
    {
        return Str::random(40) . '.' . $extension;
    }

    /**
     * @param  UploadedFile  $file The uploaded file to analyze.
     * @return string The file type.
     */
    public function getFileType(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getClientMimeType();

        if (! in_array($extension, AttachmentType::getAllowedExtensionsFlat())) {
            throw new \Exception('Incorrect file extension');
        }

        $fileType = '';

        if (strpos($mimeType, 'video') !== false) {
            $fileType = 'video';
        } elseif (strpos($mimeType, 'image') !== false) {
            $fileType = 'image';
        } elseif (in_array($extension, AttachmentType::$allowedExtensions['document'])) {
            $fileType = 'document';
        } else {
            throw new \Exception('Incorrect file type');
        }

        return $fileType;
    }

    /**
     * Resets the properties of the object to their default values.
     */
    public function reset(): void
    {
        $this->file = null;
        $this->actor = null;
        $this->payload = [];
    }
}
