
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\template\PendingTaskController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,ngo:api'])->group(function () {
  Route::post('store/task/with/content/{id}', [PendingTaskController::class, 'storeWithContent']);
});
