
<?php

use App\Enums\PermissionEnum;
use App\Enums\SubPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\template\JobController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::delete('/job/{id}', [JobController::class, "destroy"])->middleware(["userHasSubDeletePermission:" . PermissionEnum::settings->value . "," . SubPermissionEnum::setting_job->value]);
    Route::post('/job/store', [JobController::class, "store"])->middleware(["userHasSubAddPermission:" . PermissionEnum::settings->value . "," . SubPermissionEnum::setting_job->value]);
    Route::post('/job/update', [JobController::class, "update"])->middleware(["userHasSubEditPermission:" . PermissionEnum::settings->value . "," . SubPermissionEnum::setting_job->value]);
});
Route::prefix('v1')->group(function () {
    Route::get('/jobs', [JobController::class, "jobs"]);
    Route::get('/job/{id}', [JobController::class, "job"]);
});
