
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\app\news\NewsController;
use Illuminate\Support\Facades\Route;



Route::get('news', [NewsController::class, 'news']);

Route::prefix('v1')->group(function () {
  Route::get('/public/newses/{page}', [NewsController::class, "publicNewses"]);
  Route::get('/public/news/{id}', [NewsController::class, "publicNews"]);
});
Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('/auth/newses', [NewsController::class, "authNewses"]);
  Route::get('/auth/news/{id}', [NewsController::class, "authNews"]);
  Route::post('news/store', [NewsController::class, 'store']);
});
