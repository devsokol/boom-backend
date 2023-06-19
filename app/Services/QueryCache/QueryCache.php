<?php

namespace App\Services\QueryCache;

use App\Services\QueryCache\Traits\HasRequestModule;
use Closure;
use Illuminate\Support\Facades\Cache;

class QueryCache
{
    use HasRequestModule;

    private string $tag;

    private string $key;

    private string $locale;

    private array $except = [];

    // @phpstan-ignore-next-line
    private array $additionalParams = [];

    public function __construct()
    {
        $this->locale(app()->getLocale());
    }

    public function tag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function key(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function locale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function except(array $except): self
    {
        $this->except = $except;

        return $this;
    }

    public function additionalParams(array $additionalParams): self
    {
        $this->additionalParams = $additionalParams;

        return $this;
    }

    public function forever(Closure $callback): mixed
    {
        $value = Cache::tags($this->tag)->get($this->key);

        if (! is_null($value)) {
            return $value;
        }

        $value = $this->prepareCallback($callback);

        Cache::tags($this->tag)->forever($this->key, $value);

        return $value;
    }

    private function prepareCallback(Closure $callback): mixed
    {
        return (new QueryResponseAdapter($callback($this->locale)))->handle();
    }
}
