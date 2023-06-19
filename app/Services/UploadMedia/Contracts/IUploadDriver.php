<?php

namespace App\Services\UploadMedia\Contracts;

use Illuminate\Database\Eloquent\Model;

interface IUploadDriver
{
    public function setModel(Model $model): self;

    public function amountOfParamsInCast(): int;

    public function optionalParamsInCastFilledAs(): mixed;

    public function getSubfolder(): ?string;

    public function setSubfolder(string $subfolder): self;

    public function store(string $fieldName, Model $model, array $params): ?string;

    public function getPath(?string $rawPath): ?string;

    public function delete(?string $rawOriginal): void;
}
