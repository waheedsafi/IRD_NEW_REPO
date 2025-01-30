<?php

use App\Http\Controllers\api\template\general\GeneralController;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->middleware(['api.key'])->group(function () {
    Route::get('/genders', [GeneralController::class, "gender"]);
});
