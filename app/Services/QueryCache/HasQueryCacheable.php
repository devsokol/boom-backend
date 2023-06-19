<?php

namespace App\Services\QueryCache;

use App\Services\QueryCache\Observer\FlushQueryCacheObserver;
use Closure;
use Illuminate\Support\Facades\Cache;

trait HasQueryCacheable
{
    protected static function bootHasQueryCacheable(): void
    {
        static::observe(FlushQueryCacheObserver::class);
    }

    public function invalidateCache(): bool
    {
        $className = get_class($this);

        return Cache::tags($className)->flush();
    }

    protected static function exceptCacheWhenSearch(): bool
    {
        return true;
    }

    protected static function cacheExceptKeySearchParam(): string
    {
        return 'search';
    }

    public static function makeCacheByUniqueRequest(Closure $callback, ?Closure $configCallback = null): mixed
    {
        $query = new QueryCache();

        $query->tag(static::class);

        $query->exceptSearch(self::exceptCacheWhenSearch());

        $query->keySearchParam(self::cacheExceptKeySearchParam());

        if ($configCallback instanceof Closure) {
            $configCallback($query);
        }

        return $query->foreverByUniqueRequest($callback);
    }
}
