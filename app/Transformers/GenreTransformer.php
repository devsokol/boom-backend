<?php

namespace App\Transformers;

use App\Models\Genre;
use League\Fractal\TransformerAbstract;

class GenreTransformer extends TransformerAbstract
{
    public function transform(Genre $genre): array
    {
        return [
            'id' => $genre->getKey(),
            'name' => $genre->name,
            'icon' => $genre->icon,
            'placeholder' => $genre->placeholder,
        ];
    }
}
