<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageHelper
{
    public static function definePathToSaveContent(
        Model $model,
        ?string $subfolder = null,
        bool $anonymous = false,
        bool $withDate = false
    ): string
    {
        if (! $anonymous && $user = auth()->user()) {
            $userTableName = Str::slug($user->getTable());
            $modelTableName = Str::slug($model->getTable());

            $userId = $user->getKey();

            $path = sprintf('%s/%s/%s', $userTableName, $userId, $modelTableName);
        } else {
            $path = Str::slug($model->getTable());
        }

        if (! is_null($subfolder)) {
            $path = sprintf('%s/%s', $path, $subfolder);
        }

        if ($withDate) {
            $path = sprintf('%s/%s', $path, now()->format('Y-m'));
        }

        return $path;
    }

    public static function preventDuplicate(?string $fileName, bool $returnBasename = false): ?string
    {
        if (is_null($fileName)) {
            return null;
        }

        if (Storage::exists($fileName)) {
            // Split filename into parts
            $pathInfo = pathinfo($fileName);
            $extension = isset($pathInfo['extension']) ? ('.' . $pathInfo['extension']) : '';

            // Look for a number before the extension; add one if there isn't already
            if (preg_match('/(.*?)(\s\(\d+\))$/', $pathInfo['filename'], $match)) {
                // Have a number; get it
                $base = $match[1];
                $number = intval($match[2]);
            } else {
                // No number; pretend we found a zero
                $base = $pathInfo['filename'];
                $number = 0;
            }

            // Choose a name with an incremented number until a file with that name
            // doesn't exist
            do {
                $fileName = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $base . ' (' . ++$number . ')' . $extension;
            } while (Storage::exists($fileName));
        }

        if ($returnBasename) {
            return basename($fileName);
        }

        return $fileName;
    }
}
