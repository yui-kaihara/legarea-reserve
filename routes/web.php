<?php

use App\Http\Controllers\GuestController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GuestController as AdminGuestController;
use Illuminate\Support\Facades\Route;

//ユーザ予約画面
Route::get('form', [GuestController::class, 'create'])->name('guests.create');
Route::post('form/store', [GuestController::class, 'store'])->name('guests.store');
Route::get('form/complete', [GuestController::class, 'complete'])->name('guests.complete');

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
    Route::group(['middleware' => 'auth'], function(){
        Route::resource('guests', AdminGuestController::class);
        Route::post('guests/download', [AdminGuestController::class, 'download'])->name('guests.download');
        Route::post('guests/import', [AdminGuestController::class, 'import'])->name('guests.import');
        Route::resource('events', EventController::class);
    });
});
