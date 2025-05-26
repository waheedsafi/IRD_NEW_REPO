
<?php

use App\Http\Controllers\api\template\StatusController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {

  Route::get('/ngo/statuses', [StatusController::class, 'ngoBlockStatuses']);
  Route::get('/block/statuse/types', [StatusController::class, 'blockStatusesType']);
});
