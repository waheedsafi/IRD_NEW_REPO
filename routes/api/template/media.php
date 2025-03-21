
<?php

use App\Http\Controllers\api\template\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.key', "multiAuthorized:" . 'user:api,ngo:api'])->group(function () {
    // Route::get('/media/{storage}/{folder}/{filename}', [MediaController::class, "show"]);
    // Route::get('/media/{storage}/{access}/{folder}/{filename}', [MediaController::class, "showPublic"]);
    Route::get('/media', [MediaController::class, "downloadFile"]);
    // Route::get('/media/{storage}/{folder}/{folderType}/{filename}', [MediaController::class, "downloadDoc"]);
});
