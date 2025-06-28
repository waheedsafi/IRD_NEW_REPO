
<?php

use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\projects\ProjectController;
use App\Http\Controllers\api\app\projects\ProjectManagerController;
use App\Http\Controllers\api\app\projects\ProjectStoreController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'ngo:api'])->group(function () {
    Route::get('/projects', [ProjectManagerController::class, 'managers']);
});
