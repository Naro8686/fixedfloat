<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\PasswordController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('localization')->group(function () {
    Route::middleware('guest')->group(function () {
//    Route::get('register', [RegisteredUserController::class, 'create'])
//                ->name('register');
//
//    Route::post('register', [RegisteredUserController::class, 'store']);

        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store']);

//    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
//                ->name('password.request');
//
//    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
//                ->name('password.email');
//
//    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
//                ->name('password.reset');
//
//    Route::post('reset-password', [NewPasswordController::class, 'store'])
//                ->name('password.store');
    });

    Route::middleware(['auth', 'role:' . \App\Models\Role::SUPER_ADMIN])->group(function () {
        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->name('password.confirm');

        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
        Route::middleware(['verified'])->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('pages/{id}', [PageController::class, 'edit'])->name('pages.edit');
            Route::put('pages/{id}', [PageController::class, 'update'])->name('pages.update');
            Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
            Route::prefix('wallets')->name('wallets.')->group(function () {
                Route::get('/', [WalletController::class, 'index'])->name('index');
                Route::put('/{id}', [WalletController::class, 'update'])->name('update');
                Route::post('/', [WalletController::class, 'store'])->name('store');
                Route::delete('/{id}', [WalletController::class, 'destroy'])->name('destroy');
            });
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('ip-addresses', [OrderController::class, 'getIpAddresses'])->name('ips');
                Route::get('countries', [OrderController::class, 'getCountries'])->name('countries');
                Route::post('banip', [OrderController::class, 'banIp'])->name('banIp');
                Route::post('unbanip', [OrderController::class, 'unbanIp'])->name('unbanIp');
            });
            Route::resource('orders', OrderController::class);
            Route::resource('users', UserController::class);
        });
    });
});

