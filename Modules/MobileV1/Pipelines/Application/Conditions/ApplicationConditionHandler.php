<?php

declare(strict_types=1);

namespace Modules\MobileV1\Pipelines\Application\Conditions;

use App\Models\Actor;
use App\Models\Role;
use Illuminate\Http\Response;

class ApplicationConditionHandler
{
    private array $requirements = [];

    private Actor $actor;

    public function __construct(private Role $role)
    {
        /* @phpstan-ignore-next-line */
        $this->actor = auth()->user();
    }

    public function getActor(): Actor
    {
        return $this->actor;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRequirement(string $text): self
    {
        $this->requirements[] = $text;

        return $this;
    }

    public function throwError(string $error): never
    {
        abort(Response::HTTP_FAILED_DEPENDENCY, $error);
    }

    public function abortIfExistsRequirements(): void
    {
        if (! empty($this->requirements)) {
            $this->throwError(__('To continue you need: ') . implode(',', $this->requirements));
        }
    }
}
