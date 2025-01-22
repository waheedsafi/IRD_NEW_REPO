
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\template\about\AboutController;
use Illuminate\Support\Facades\Route;



// Route::get('abouts', [AboutController::class, 'abouts']);

Route::prefix('v1')->group(function () {
Route::get('abouts', [AboutController::class, 'abouts']);

});
Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('about/{id}', [AboutController::class, "about"]);
  Route::get('about/store', [AboutController::class, "store"]);
  Route::post('/about/update', [AboutController::class, 'update']);
});
