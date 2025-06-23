<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\ActivityLogController;

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard'); // Create this view
    })->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::name('settings.')->prefix('settings')->group(function () {
        Route::get('/', [SystemSettingController::class, 'index'])->name('index');

        Route::prefix('floors')->name('floors.')->group(function () {
            Route::get('/', [SystemSettingController::class, 'floorsIndex'])->name('index');
            Route::get('/create', [SystemSettingController::class, 'floorsCreate'])->name('create');
            Route::post('/', [SystemSettingController::class, 'floorsStore'])->name('store');
            Route::get('/{floor}/edit', [SystemSettingController::class, 'floorsEdit'])->name('edit');
            Route::put('/{floor}', [SystemSettingController::class, 'floorsUpdate'])->name('update');
            Route::delete('/{floor}', [SystemSettingController::class, 'floorsDestroy'])->name('destroy');
        });

        Route::prefix('rooms')->name('rooms.')->group(function () {
            Route::get('/', [SystemSettingController::class, 'roomsIndex'])->name('index');
            Route::get('/create', [SystemSettingController::class, 'roomsCreate'])->name('create');
            Route::post('/', [SystemSettingController::class, 'roomsStore'])->name('store');
            Route::get('/{room}/edit', [SystemSettingController::class, 'roomsEdit'])->name('edit');
            Route::put('/{room}', [SystemSettingController::class, 'roomsUpdate'])->name('update');
            Route::delete('/{room}', [SystemSettingController::class, 'roomsDestroy'])->name('destroy');
        });

        Route::prefix('labtests')->name('labtests.')->group(function () {
            Route::get('/', [SystemSettingController::class, 'labTestsIndex'])->name('index');
            Route::get('/create', [SystemSettingController::class, 'labTestsCreate'])->name('create');
            Route::post('/', [SystemSettingController::class, 'labTestsStore'])->name('store');
            Route::get('/{availableLabTest}/edit', [SystemSettingController::class, 'labTestsEdit'])->name('edit');
            Route::put('/{availableLabTest}', [SystemSettingController::class, 'labTestsUpdate'])->name('update');
            Route::delete('/{availableLabTest}', [SystemSettingController::class, 'labTestsDestroy'])->name('destroy');
        });
    });

    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity_logs.show');
    Route::delete('activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity_logs.destroy');
    Route::post('activity-logs/clear-old', [ActivityLogController::class, 'clearOldLogs'])->name('activity_logs.clearOld');

});



require __DIR__.'/auth.php';
