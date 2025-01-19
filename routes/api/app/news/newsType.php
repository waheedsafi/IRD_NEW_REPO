
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\app\news\NewsTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
  Route::get('/public/newses', [NewsTypeController::class, "publicNewses"]);
  Route::get('/public/news/{id}', [NewsTypeController::class, "publicNews"]);
});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('/news-types', [NewsTypeController::class, "newsTypes"])->middleware(["hasViewPermission:" . PermissionEnum::news->value]);
  Route::delete('/news/{id}', [NewsTypeController::class, "destroy"])->middleware(["hasDeletePermission:" . PermissionEnum::news->value]);
  Route::get('/news/{id}', [NewsTypeController::class, "news"])->middleware(["hasViewPermission:" . PermissionEnum::news->value]);
  Route::post('/news/store', [NewsTypeController::class, "store"])->middleware(["hasAddPermission:" . PermissionEnum::news->value]);
  Route::post('/news/update', [NewsTypeController::class, "update"])->middleware(["hasEditPermission:" . PermissionEnum::news->value]);
});
