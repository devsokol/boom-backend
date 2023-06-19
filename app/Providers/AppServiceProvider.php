<?php

namespace App\Providers;

use App\Http\Kernel;
use Carbon\CarbonInterval;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $loader = AliasLoader::getInstance();

            //IDE helper
            if (class_exists('\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider')) {
                $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            }

            // Telescope
            if (class_exists('\Laravel\Telescope\TelescopeServiceProvider::class')) {
                $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
                $this->app->register(TelescopeServiceProvider::class);
            }

            //Debugbar
            if (class_exists('\Barryvdh\Debugbar\ServiceProvider')) {
                $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
                $loader->alias('Debugbar', \Barryvdh\Debugbar\Facades\Debugbar::class);
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(! app()->isProduction());
        // Model::preventAccessingMissingAttribute(! app()->isProduction());
        // Model::preventSilentlyDiscardingAttributes(! app()->isProduction());
        // Test
        // Model::shouldBeStrict(! app()->isProduction());

        //if (app()->isProduction()) {
        DB::whenQueryingForLongerThan(CarbonInterval::seconds(5), function (Connection $connection) {
            logger()->debug('[SeveralQuery] QueryingForLongerThan:' . $connection->totalQueryDuration());
        });

        DB::listen(static function ($q) {
            if ($q->time > CarbonInterval::seconds(4)->totalMilliseconds) {
                logger()->debug('[SingleQuery] QueryingForLongerThan:' . $q->sql, $q->bindings);
            }
        });

        $kernel = app(Kernel::class);

        $kernel->whenRequestLifecycleIsLongerThan(CarbonInterval::seconds(4), function () {
            logger()->debug('[Http]RequestLifecycleIsLongerThan:' . request()->url());
        });
        //}

        if (config('app.force_https')) {
            URL::forceScheme('https');
        }
    }
}
