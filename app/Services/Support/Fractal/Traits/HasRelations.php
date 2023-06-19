<?php

namespace App\Services\Support\Fractal\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use League\Fractal\TransformerAbstract;

trait HasRelations
{
    public function itemHasRelation(Model $model, string $relation, TransformerAbstract|callable $transformer): mixed
    {
        if ($model->relationLoaded($relation) && $model->{$relation}) {
            if ($model->{$relation} instanceof Collection) {
                return $this->collection($model->{$relation}, $transformer);
            }

            return $this->item($model->{$relation}, $transformer);
        }

        try {
            if ($model->{$relation}() instanceof BelongsTo
                || $model->{$relation}() instanceof HasOne
                || $model->{$relation}() instanceof HasOneThrough
            ) {
                return $this->primitive(new \stdClass());
            }
        } catch (\Exception $e) {
            return $this->primitive([]);
        }

        return $this->primitive([]);
    }
}
