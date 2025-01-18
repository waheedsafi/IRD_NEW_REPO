
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\app\ngo\NgoTypeController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {

  Route::get('/ngo/types', [NgoTypeController::class, 'types'])->middleware(["hasAddPermission:" . PermissionEnum::settings->value]);
});
