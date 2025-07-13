
<?php

use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\schedule\ScheduleController;


Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::get('/schedules/prepare', [ScheduleController::class, 'prepareSchedule']);
});
