
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\template\Staff\StaffController;
use Illuminate\Support\Facades\Route;




Route::prefix('v1')->group(function () {
  Route::get('/staff/director', [StaffController::class, "director"]);
  Route::get('/staff/manager', [StaffController::class, "manager"]);
  Route::get('/staff/technicalSupports', [StaffController::class, "technicalSupports"]);
 
});
Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('/staff/store', [StaffController::class, "store"]);
  Route::get('/staff/{id}', [StaffController::class, "staff"]);
  Route::post('/staff/upadate', [StaffController::class, 'update']);
});
