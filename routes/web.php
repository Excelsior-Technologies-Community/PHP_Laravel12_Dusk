<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect('/login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm']);
    Route::post('/register', [AuthController::class, 'register']);
    
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotForm']);
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);
    
    // Reset Password
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    // Email Verification
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed'])->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);
});

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/users', [AuthController::class, 'users']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Admin routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [AuthController::class, 'adminDashboard']);
    });
});