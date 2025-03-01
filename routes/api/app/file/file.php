
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\app\file\FileController;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->middleware(['api.key', "doubleAuthorized:" . 'user:api,ngo:api'])->group(function () {
  Route::post('checklist/file/upload', [FileController::class, 'checklistUploadFile'])->withoutMiddleware('throttle');
  Route::post('single/checklist/file/upload', [FileController::class, 'singleChecklistFileUpload'])->withoutMiddleware('throttle');
});
