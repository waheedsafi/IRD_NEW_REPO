
<?php

use App\Http\Controllers\api\template\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/user/profile/picture-update', [ProfileController::class, 'updateUserPicture']);
    Route::post('/user/profile/info/update', [ProfileController::class, 'updateUserProfileInfo']);
});
Route::prefix('v1')->middleware(["authorized:" . 'ngo:api'])->group(function () {
    Route::put('/ngos/picture', [ProfileController::class, 'updateNgoPicture']);
    Route::post('/ngos/profile', [ProfileController::class, 'updateNgoProfileInfo']);
    Route::get('/ngos/profile/{id}', [ProfileController::class, 'ngoProfileInfo']);
});

Route::prefix('v1')->middleware(["authorized:" . 'donor:api'])->group(function () {
    Route::post('/donor/profile/update', [ProfileController::class, 'updateDonorProfileInfo']);
    Route::post('/donor/profile/picture-update', [ProfileController::class, 'updateDonorPicture']);
});
Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,ngo:api,donor:api'])->group(function () {
    Route::delete('/delete/profile-picture', [ProfileController::class, 'deleteProfilePicture']);
});
