
<?php

use App\Http\Controllers\api\app\director\DirectorController;








Route::prefix('v1')->group(function () {});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {


  Route::get('/director/details/{ngo_id}', [DirectorController::class, 'directorDetails']);
});

// ngo user 

// Route::get('/ngo/details/{id}', [NgoController::class, 'ngoDetail']);

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'ngo:api'])->group(function () {});
