<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\MasterUserController;
use App\Http\Controllers\Admin\MasterRoleController;
use App\Http\Controllers\admin\FtpUploadController;

// Auth Routes (tambah middleware guest)
Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/', [AuthController::class, 'login']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])
    ->name('dashboard');


    // Admin Routes
    Route::middleware(['role:1'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('master_user', MasterUserController::class);
        Route::resource('master_role', MasterRoleController::class);
        Route::get('/users/autocomplete', [MasterUserController::class, 'autocomplete']);
        Route::get('/master_user', [MasterUserController::class, 'index'])->name('master_user.index');
        Route::get('/master_user/export/{type}', [MasterUserController::class, 'export'])->name('master_user.export');
        Route::post('/master_user/import', [MasterUserController::class, 'import'])->name('master_user.import');
        Route::get('/ftp', [FtpUploadController::class, 'index'])->name('ftp.index');
        Route::post('/ftp/upload', [FtpUploadController::class, 'upload'])->name('ftp.upload');
        Route::get('/ftp/delete/{file}', [FtpUploadController::class, 'delete'])->name('ftp.delete');
        Route::get('/ftp/view/{file}', [FtpUploadController::class, 'view'])->name('ftp.view');
    });
});
