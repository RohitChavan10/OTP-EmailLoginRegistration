<?php

use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



Route::get('/', fn() => view('welcome'));

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//otp_registration
Route::prefix('otp')->group(function () {
    Route::post('login/send', [OtpController::class, 'sendLogin'])->name('otp.login.send');
    Route::post('login/verify', [OtpController::class, 'verifyLogin'])->name('otp.login.verify');
    Route::post('register/verify', [OtpController::class, 'verify'])->name('otp.register.verify');
});


Route::get('/log-test', function() {
    logger()->info('Test OTP logging');
    return 'Check storage/logs/laravel.log';
});


Route::post('/otp/register/send', [OtpController::class, 'sendRegister'])->name('otp.register.send');




// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
