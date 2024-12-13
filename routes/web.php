<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'loadRegister']);
    Route::post('/register', [AuthController::class, 'userRegister'])->name('userRegister');

    Route::get('/', [AuthController::class, 'loadLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'userLogin'])->name('userLogin');
});

Route::middleware(['userAuth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
});

Route::middleware(['isAuthenticate'])->group(function () {
    Route::get('/subscription', [SubscriptionController::class, 'LoadSubscription'])->name('subscription');
    Route::post('/get-plan-details',[SubscriptionController::class,'getPlanDetails'])->name('getPlanDetails');

    Route::get('/logout',[AuthController::class,'logout'])->name('logout');
});
