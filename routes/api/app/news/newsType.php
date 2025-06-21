
<?php

use App\Enums\PermissionEnum;
use App\Enums\SubPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\news\NewsTypeController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('/news-types', [NewsTypeController::class, "newsTypes"])->middleware(["userHasSubViewPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_news_type->value]);
  Route::delete('/news-types/{id}', [NewsTypeController::class, "destroy"])->middleware(["userHasSubDeletePermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_news_type->value]);
  Route::get('/news-types/{id}', [NewsTypeController::class, "newsType"])->middleware(["userHasSubViewPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_news_type->value]);
  Route::post('/news-types/store', [NewsTypeController::class, "store"])->middleware(["userHasSubStorePermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_news_type->value]);
  Route::post('/news-types/update', [NewsTypeController::class, "update"])->middleware(["userHasSubEditPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_news_type->value]);
});
