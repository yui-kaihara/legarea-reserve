<?php

use App\Http\Controllers\GuestController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GuestController as AdminGuestController;
use Illuminate\Support\Facades\Route;

//ユーザ予約画面
Route::get('/', [GuestController::class, 'create'])->name('guests.create');
Route::resource('guests', GuestController::class)->only(['store']);

//管理画面
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/admin', function () {
        return redirect('admin/guests');
    });
});
Route::name('admin.')->prefix('admin')->group(function () {
    Route::resource('guests', AdminGuestController::class);
    Route::post('guests/download', [AdminGuestController::class, 'download'])->name('guests.download');
    Route::resource('events', EventController::class);
});
