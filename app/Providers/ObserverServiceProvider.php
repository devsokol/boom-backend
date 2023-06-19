<?php

namespace App\Providers;

use App\Models\Actor;
use App\Models\Application;
use App\Models\ApplicationSelftape;
use App\Models\Attachment;
use App\Models\Audition;
use App\Models\Project;
use App\Models\Role;
use App\Models\RoleMaterial;
use App\Observers\ActorObserver;
use App\Observers\ApplicationObserver;
use App\Observers\ApplicationSelftapeObserver;
use App\Observers\AttachmentObserver;
use App\Observers\AuditionObserver;
use App\Observers\ProjectObserver;
use App\Observers\RoleMaterialObserver;
use App\Observers\RoleObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Actor::observe(ActorObserver::class);
        Application::observe(ApplicationObserver::class);
        ApplicationSelftape::observe(ApplicationSelftapeObserver::class);
        Audition::observe(AuditionObserver::class);
        Project::observe(ProjectObserver::class);
        Role::observe(RoleObserver::class);
        RoleMaterial::observe(RoleMaterialObserver::class);
        Attachment::observe(AttachmentObserver::class);
    }
}
