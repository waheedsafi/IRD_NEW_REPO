
<?php

use App\Enums\PermissionEnum;
use Illuminate\Support\Facades\Route;



Route::get('news', [NewsController::class, 'news']);

Route::prefix('v1')->group(function () {
  Route::get('/public/newses/{page}', [NewsController::class, "publicNewses"]);
  Route::get('/public/newses/{page}', [NewsController::class, "publicNewses"]);
  // Route::get('/public/news/{id}', [NewsController::class, "publicNews"]);
  Route::get('/public/news/{id}', [NewsController::class, "authNews"]);
});
Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('/user/newses/{page}', [NewsController::class, "authNewses"]);
  Route::get('/user/news/{id}', [NewsController::class, "authNews"]);
  Route::post('news/store', [NewsController::class, 'store']);
});
