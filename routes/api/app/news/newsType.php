
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\app\news\NewsTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('/news-types', [NewsTypeController::class, "newsTypes"])->middleware(["hasViewPermission:" . PermissionEnum::settings->value]);
  Route::delete('/news-types/{id}', [NewsTypeController::class, "destroy"])->middleware(["hasDeletePermission:" . PermissionEnum::settings->value]);
  Route::get('/news-types/{id}', [NewsTypeController::class, "newsType"])->middleware(["hasViewPermission:" . PermissionEnum::settings->value]);
  Route::post('/news-types/store', [NewsTypeController::class, "store"])->middleware(["hasAddPermission:" . PermissionEnum::settings->value]);
  Route::post('/news-types/update', [NewsTypeController::class, "update"])->middleware(["hasEditPermission:" . PermissionEnum::settings->value]);
});
