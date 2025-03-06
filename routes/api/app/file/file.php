
<?php

use App\Enums\PermissionEnum;
use App\Enums\SubPermissionEnum;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\app\file\FileController;



Route::prefix('v1')->middleware(['api.key', "doubleAuthorized:" . 'user:api,ngo:api'])->group(function () {
  Route::post('checklist/file/upload', [FileController::class, 'checklistUploadFile'])->withoutMiddleware('throttle');
  Route::post('single/checklist/file/upload', [FileController::class, 'singleChecklistFileUpload'])->withoutMiddleware('throttle');
  Route::post('ngo/register/signed/form/upload', [FileController::class, 'registerSignedFormUpload'])->withoutMiddleware('throttle');
});
