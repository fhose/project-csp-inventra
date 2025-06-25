<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| Rute Publik (Tidak Perlu Login)
|--------------------------------------------------------------------------
| Rute ini adalah pintu masuk aplikasi Anda.
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


/*
|--------------------------------------------------------------------------
| Rute Terproteksi (Wajib Login dengan Token)
|--------------------------------------------------------------------------
| Semua rute di sini memerlukan token otentikasi.
*/
Route::middleware('auth:sanctum')->group(function () {
    // Rute untuk logout dan mengambil data user
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Rute untuk data aplikasi
    Route::get('/dashboard', DashboardController::class);

    // Rute inventaris
    Route::apiResource('items', ItemController::class);
    Route::get('/item-conditions', [ItemController::class, 'conditions']);

    // Rute peminjaman
    // Harus didefinisikan SEBELUM apiResource
    Route::get('/loans/active', [LoanController::class, 'active']);
    // Route::post('/loans/{id}/extend', [LoanController::class, 'extend']);
    Route::post('/loans/{id}/approve', [LoanController::class, 'approve']);
    Route::post('/loans/{id}/reject', [LoanController::class, 'reject']);
    Route::post('/loans/{id}/request-extension', [LoanController::class, 'requestExtension']);
    Route::post('/loans/{id}/approve-extension', [LoanController::class, 'approveExtension']);
    Route::post('/loans/{id}/reject-extension', [LoanController::class, 'rejectExtension']);

    // Ini terakhir!
    Route::apiResource('loans', LoanController::class);

});