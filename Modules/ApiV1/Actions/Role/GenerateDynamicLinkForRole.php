<?php

namespace Modules\ApiV1\Actions\Role;

use App\Models\Role;
use Illuminate\Support\Str;
use App\Services\FirebaseDynamicLinks\DynamicLink;

class GenerateDynamicLinkForRole
{
    public function handle(Role $role, bool $updateModel = false): string
    {
        $role->loadMissing('project');

        $title = $role->name;
        $description = $role->description;
        $projectImage = $role->relationLoaded('project') ? $role->project->getPlaceholder() : '';
        $path = 'role_id=' . $role->getKey();

        $shortDescription = Str::limit($description, 100, '...');

        $dynamicLinkObject = (new DynamicLink($title, $shortDescription, $projectImage))
            ->setPath($path)
            ->handle();

        $link = $dynamicLinkObject->response(returnOnlyLink: true);

        if ($updateModel) {
            $role->update(['dynamic_link' => $link]);
        }

        return $link;
    }
}
