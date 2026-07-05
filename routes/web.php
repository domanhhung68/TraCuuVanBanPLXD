<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteLawController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LawController;
use App\Http\Controllers\WordImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Trang chủ - Khách hàng chưa đăng nhập có thể xem
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/tim-van-ban', [LawController::class, 'search'])->name('law.search');

Route::get('/van-ban/{id}', [LawController::class, 'show'])->name('law.show');
Route::get('/laws/files/{id}/download', [LawController::class, 'downloadFile'])->name('law.file.download');

// Routes xác thực (không cần đăng nhập)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

// Routes quên mật khẩu
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update')->middleware('guest');

// Routes demo UI admin (không cần backend)
Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
Route::get('/admin-ui/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.ui.dashboard');
Route::get('/admin-ui/legal-documents', [LawController::class, 'index'])->name('admin.ui.legal-documents');
Route::get('/admin-ui/legal-documents/create', [LawController::class, 'create'])->name('admin.ui.law.create');
Route::get('/admin-ui/legal-documents/{law}/edit', [LawController::class, 'edit'])->name('admin.ui.law.edit');
Route::post('/admin-ui/legal-documents/import-word', [WordImportController::class, 'import'])->name('admin.ui.law.import-word');
Route::post('/admin-ui/legal-documents', [LawController::class, 'store'])->name('admin.ui.law.store');
Route::put('/admin-ui/legal-documents/{law}', [LawController::class, 'update'])->name('admin.ui.law.update');
Route::delete('/admin-ui/legal-documents/{law}', [LawController::class, 'destroy'])->name('admin.ui.law.destroy');
Route::get('/admin-ui/users', [AdminUserController::class, 'index'])->name('admin.ui.users');

// Routes cần xác thực
Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfileForm'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/profile/favorites', [FavoriteLawController::class, 'profileIndex'])->name('profile.favorites');
    Route::post('/favorites', [FavoriteLawController::class, 'store'])->name('favorites.store');
    Route::match(['POST', 'DELETE'], '/favorites/{law_id}', [FavoriteLawController::class, 'destroy'])->name('favorites.destroy');
    
    // Routes chỉ cho Khách hàng
    Route::middleware('role:customer')->group(function () {
        Route::get('/customer/dashboard', [DashboardController::class, 'customerDashboard'])->name('customer.dashboard');
    });
    
    // Routes chỉ cho Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    });
});

