<?php

namespace App\Services\QueryCache\Traits;

use Closure;

trait HasRequestModule
{
    private bool $exceptWhenSearchParam = true;

    private string $keySearchParam = 'search';

    public function exceptSearch(bool $except = true): self
    {
        $this->exceptWhenSearchParam = $except;

        return $this;
    }

    public function keySearchParam(string $key): self
    {
        $this->keySearchParam = $key;

        return $this;
    }

    public function foreverByUniqueRequest(Closure $callback): mixed
    {
        if ($this->excludeWhenUrlContainParams($this->except)) {
            return $callback($this->locale);
        }

        $this->generateCacheKeyFromRequest();

        if ($this->isExistsSearchParam()) {
            return $this->prepareCallback($callback);
        }

        return $this->forever($callback);
    }

    private function generateCacheKeyFromRequest(): void
    {
        $url = request()->url();

        $queryParams = request()->query();

        if (! empty($this->additionalKeys)) {
            $queryParams = array_merge($queryParams, $this->additionalKeys);
        }

        $queryParams = array_merge($queryParams, ['locale' => $this->locale]);

        ksort($queryParams);

        $queryString = http_build_query($queryParams);

        $uniqueUrl = "{$url}?{$queryString}";

        $this->key(hash('sha256', $uniqueUrl));
    }

    private function excludeWhenUrlContainParams(array $params): bool
    {
        $queryParams = request()->query();

        foreach ($params as $param) {
            if (isset($queryParams[$param]) && ! empty($queryParams[$param])) {
                return true;
            }
        }

        return false;
    }

    private function isExistsSearchParam(): bool
    {
        return request()->has($this->keySearchParam);
    }
}
