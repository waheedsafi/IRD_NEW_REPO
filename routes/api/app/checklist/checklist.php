
<?php

use App\Http\Controllers\api\app\checklist\CheckListController;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->middleware(['api.key', "doubleAuthorized:" . 'user:api,ngo:api'])->group(function () {
  Route::get('checklists', [CheckListController::class, 'checklists']);
  Route::get('ngo/checklist/types', [CheckListController::class, 'checklistTypes']);
  Route::get('project/register/checklist', [CheckListController::class, 'projectRegister']);
  Route::get('ngo/register/checklist', [CheckListController::class, 'ngoRegister']);
  Route::get('ngo/register/abroad/director-checklist', [CheckListController::class, 'ngoRegisterAbroadDirector']);
});

Route::prefix('v1')->middleware(['api.key', "authorized:" . 'user:api'])->group(function () {
  Route::get('ngo/checklist/{id}', [CheckListController::class, 'checklist']);
  Route::get('checklist/store', [CheckListController::class, 'store']);
  Route::delete('delete/checklist/{id}', [CheckListController::class, 'destroy']);
  Route::post('checklist/update', [CheckListController::class, 'update']);
});
