<?php

namespace App\Models;

use App\Services\QueryCache\HasQueryCacheable;
use App\Traits\Model\HasProtectedRouteBinding;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttachmentType extends BaseModel
{
    use HasFactory;
    use HasProtectedRouteBinding;
    use HasQueryCacheable;

    public $timestamps = false;

    public static array $allowedExtensions = [
        'document' => ['pdf', 'doc', 'docx', 'odt', 'rtf', 'txt', 'ods', 'csv', 'xls', 'xlsx'],
        'image' => ['png', 'jpg', 'jpeg', 'bmp', 'gif', 'heic', 'webp'],
        'video' => ['mov', 'mp4', '3gp'],
    ];

    public static function getAllowedExtensionsFlat(): array
    {
        $flatArray = [];

        foreach (self::$allowedExtensions as $extensionGroup) {
            foreach ($extensionGroup as $extension) {
                $flatArray[] = $extension;
            }
        }

        return $flatArray;
    }

    public static function getFormatFile(string $mimeType): ?string
    {
        foreach (self::$allowedExtensions as $key => $extensionGroup) {
            if (in_array($mimeType, $extensionGroup)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Get the type.
     *
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function getType(string $name): self
    {
        return self::where('name', $name)->firstOrFail();
    }

    /**
     * Get the headshot type.
     *
     * @return \App\Models\Type
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function getHeadshot()
    {
        return self::where('name', 'headshot')->firstOrFail();
    }

    /**
     * Retrieves the video type IDs from the database that
     * correspond to the following four video names:
     * "selftape", "showreel", "presentation", and "other".
     *
     * @return array An array of video type IDs.
     */
    public static function getVideoTypesId(): array
    {
        return self::whereIn('name', [
            'selftape',
            'showreel',
            'presentation',
            'other',
        ])->pluck('id')->toArray();
    }

    /**
     * Get the collection of material types.
     *
     * @return \Illuminate\Database\Eloquent\Collection A collection of model objects representing material types.
     */
    public static function getMaterialTypes(): Collection
    {
        return self::whereIn('name', [
            'mt-reference-image',
            'mt-audition-script',
            'mt-video-clip',
            'mt-other',
        ])->get();
    }

    /**
     * Define a one-to-many relationship with the Attachment model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
