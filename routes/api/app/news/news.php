
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\app\news\NewsController;
use Illuminate\Support\Facades\Route;



  Route::get('news',[NewsController::class, 'showNews']);

  
Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {

  
  Route::post('news/store', [NewsController::class, 'store']);




});
