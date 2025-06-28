
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\projects\ProjectManagerController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'ngo:api'])->group(function () {
    Route::get('/projects', [ProjectManagerController::class, 'managers']);
});
