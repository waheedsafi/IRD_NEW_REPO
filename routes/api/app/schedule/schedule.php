
<?php

use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\projects\ProjectController;
use App\Http\Controllers\api\app\projects\ProjectStoreController;
use App\Http\Controllers\api\app\schedule\ScheduleController;


Route::prefix('v1')->middleware(["multiAuthorized:" . 'ngo:api,user:api'])->group(function () {
    Route::get('/schedules/prepare', [ScheduleController::class, 'specialProject']);
});
