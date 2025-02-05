
<?php

use App\Http\Controllers\api\app\director\DirectorController;
use Illuminate\Support\Facades\Route;








Route::prefix('v1')->group(function () {});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('/ngo/director/{ngo_id}', [DirectorController::class, 'ngoDirector']);
});

// ngo user 


Route::prefix('v1')->middleware(['api.key', "authorized:" . 'ngo:api'])->group(function () {});
