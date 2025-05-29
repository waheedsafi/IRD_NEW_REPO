
<?php

use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\projects\ProjectController;

Route::get('/projects', [ProjectController::class, 'index']);
Route::prefix('v1')->middleware(["multiAuthorized:" . 'ngo:api'])->group(function () {
    Route::get('/projects/register-form/{id}', [ProjectController::class, 'startRegisterForm'])->middleware(["userHasMainAddPermission:" . PermissionEnum::projects->value]);
    Route::get('/projects', [ProjectController::class, 'index'])->middleware(["userHasMainAddPermission:" . PermissionEnum::projects->value]);
    Route::post('/projects/pending-task/{id}', [ProjectController::class, 'destroyPendingTask']);
});
