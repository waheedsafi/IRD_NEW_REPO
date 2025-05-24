
<?php

use App\Http\Controllers\api\app\ngo\NgoPdfController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,ngo:api'])->group(function () {
  Route::get('/ngo/generate/registeration/{id}', [NgoPdfController::class, 'generateForm']);
});
