<?php

namespace App\Services\QueryCache\Observer;

use Illuminate\Database\Eloquent\Model;

class FlushQueryCacheObserver
{
    public function created(Model $model): void
    {
        $this->invalidateCache($model);
    }

    public function updated(Model $model): void
    {
        $this->invalidateCache($model);
    }

    public function saved(Model $model): void
    {
        $this->invalidateCache($model);
    }

    public function deleted(Model $model): void
    {
        $this->invalidateCache($model);
    }

    public function forceDeleted(Model $model): void
    {
        $this->invalidateCache($model);
    }

    public function restored(Model $model): void
    {
        $this->invalidateCache($model);
    }

    protected function invalidateCache(Model $model): void
    {
        // @phpstan-ignore-next-line
        $model->invalidateCache();
    }
}
