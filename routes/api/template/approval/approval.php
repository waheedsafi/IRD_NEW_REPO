
<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\api\template\approval\ApprovalController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::post('approvals', [ApprovalController::class, 'approvals'])->middleware(["userHasMainAddPermission:" . PermissionEnum::approval->value]);
});
