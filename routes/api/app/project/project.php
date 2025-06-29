
<?php

use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\projects\ProjectController;
use App\Http\Controllers\api\app\projects\ProjectStoreController;

Route::get('/projects', [ProjectController::class, 'index']);
Route::prefix('v1')->middleware(["multiAuthorized:" . 'ngo:api'])->group(function () {
    Route::get('/projects/register-form/{id}', [ProjectController::class, 'startRegisterForm'])->middleware(["userHasMainAddPermission:" . PermissionEnum::projects->value]);
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectStoreController::class, 'store']);
    Route::post('/projects/pending-task/{id}', [ProjectController::class, 'destroyPendingTask']);
    Route::get('/projects/count', [ProjectController::class, "ngoProjects"]);
});
