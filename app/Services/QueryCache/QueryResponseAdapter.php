<?php

namespace App\Services\QueryCache;

use App\Services\QueryCache\Response\SpatieFractalResponse;

class QueryResponseAdapter
{
    private mixed $data;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }

    public function handle(): mixed
    {
        if ($this->data instanceof \Spatie\Fractal\Fractal) {
            $this->data = (new SpatieFractalResponse($this->data))->handle();
        }

        return $this->data;
    }
}
