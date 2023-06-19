<?php
declare(strict_types=1);

use App\Services\FirebaseDynamicLinks\DynamicLink;

// It is need for to check a pod health in k8s
Route::get('/', function () {
    return response()->json(['message' => 'OK']);
});
