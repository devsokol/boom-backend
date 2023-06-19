<?php

namespace App\Jobs;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProjectSmoothDeleting implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The project instance.
     *
     * @var \App\Models\Project
     */
    public $project;

    /**
     * Create a new job instance.
     *
     * @param  App\Models\Project  $project
     * @return void
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->project->status === ProjectStatus::DELETED) {
            $this->project->delete();
        }
    }
}
