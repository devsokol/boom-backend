<?php

namespace App\Console\Commands;

use App\Models\ActorAttachments;
use App\Models\Attachment;
use App\Models\AttachmentType;
use App\Services\Common\Attachment\AttachmentService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:attachments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from media tables to attachments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AttachmentService $attachmentService)
    {
        if (Schema::hasTable('selftapes')) {
            $this->info('\App\Models\Selftape');

            \App\Models\Selftape::all()
                ->each(function ($item) {
                    if (! $item->is_migrate) {
                        $video = $item->getRawOriginal('video');

                        $attachment = $this->addAttachment([
                            'name' => $this->getFileName($video),
                            'mime_type' => $this->getExtension($video),
                            'attachment_repository' => $video,
                            'attachment_type_id' => AttachmentType::getType('selftape')->getKey(),
                            'actor_id' => $item->actor_id,
                        ]);

                        if ($attachment) {
                            $item->is_migrate = true;
                            $item->save();
                        }
                    }
                });
        }

        if (Schema::hasTable('headshots')) {
            $this->info('\App\Models\Headshot');

            \App\Models\Headshot::all()
                ->each(function ($item) {
                    if (! $item->is_migrate) {
                        $attachment = $this->addAttachment([
                            'name' => $this->getFileName($item->headshot),
                            'mime_type' => $this->getExtension($item->headshot),
                            'attachment_repository' => $item->headshot,
                            'attachment_type_id' => AttachmentType::getType('headshot')->getKey(),
                            'actor_id' => $item->actor_id,
                        ]);

                        if ($attachment) {
                            $item->is_migrate = true;
                            $item->save();
                        }
                    }
                });
        }

        // if (Schema::hasTable('application_selftape_materials')) {
        //     $this->processMaterials(
        //         \App\Models\ApplicationSelftapeMaterial::class,
        //         \App\Models\ApplicationSelftape::class,
        //         'application_selftape_id'
        //     );
        // }

        if (Schema::hasTable('role_materials')) {
            $this->processMaterials(
                \App\Models\RoleMaterial::class,
                \App\Models\RoleAttachment::class,
                'role_id'
            );
        }
    }

    /**
     * @param array
     */
    public function addAttachment(array $data): ?Attachment
    {
        return DB::transaction(function () use ($data) {
            try {
                $attachment = Attachment::create(
                    Arr::except($data, ['actor_id'])
                );

                if (isset($data['actor_id'])) {
                    ActorAttachments::create([
                        'actor_id' => $data['actor_id'],
                        'attachment_id' => $attachment->id,
                    ]);
                }

                $this->info('Moved to attachment: ' . $data['name']);

                return $attachment;
            } catch (\Exception $e) {
                $this->error('skipped ' . $data['attachment_repository']);
            }
        });
    }

    /**
     * @param string
     */
    public function processMaterials(
        string $model,
        string $bindingModel,
        string $field
    ): void {
        $this->info($model);

        $model::all()
            ->each(function ($item) use ($bindingModel, $field) {
                try {
                    if (! $item->is_migrate) {
                        $materialTypeName = \App\Models\MaterialType::findOrFail($item['material_type_id'])->name;

                        $attachmentType = AttachmentType::where('slug', $materialTypeName)->firstOrFail();

                        $attachmentRepository = $this->getRelativePath($item->getRawOriginal('attachment'));

                        $attachment = $this->addAttachment([
                            'name' => $this->getFileName($item->attachment),
                            'mime_type' => $this->getExtension($item->attachment),
                            'attachment_repository' => $attachmentRepository,
                            'attachment_type_id' => $attachmentType->getKey(),
                        ]);

                        if ($attachment) {
                            $bindingModel::create([
                                'attachment_id' => $attachment->id,
                                $field => $item[$field],
                            ]);

                            $item->is_migrate = true;
                            $item->save();
                        }
                    }
                } catch (\Exception $e) {
                    dd($e);
                    $this->error('skipped id: ' . $item->id . ' incorrect type file!');
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
     * @param string
     */
    public function getRelativePath(string $path): string
    {
        return '/' . substr($path, strrpos($path, '|') + 1);
    }
}
