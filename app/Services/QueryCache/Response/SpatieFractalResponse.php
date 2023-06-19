<?php

namespace App\Services\QueryCache\Response;

class SpatieFractalResponse
{
    private mixed $data;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }

    public function handle(): array
    {
        return $this->data->toArray();
    }
}
