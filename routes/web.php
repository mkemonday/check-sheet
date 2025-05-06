<?php

use App\Livewire\Daily\DailyCheckForm;
use App\Livewire\Daily\DailyCheckMatrix;
use App\Livewire\Daily\UploadPhoto;
use App\Livewire\Dashboard\MachineInspectionReport;
use App\Livewire\Permissions\Manage as PermissionsManage;
use App\Livewire\Roles\Manage;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Setup\Areas;
use App\Livewire\Setup\CheckItems;
use App\Livewire\Setup\CheckMethods;
use App\Livewire\Users\AssignRoles;
use App\Livewire\Users\Manage as UsersManage;
use App\Models\DailyCheck;
use Illuminate\Support\Facades\Route;

Route::get('/link-storage', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    if (file_exists($link)) {
        return "Link already exists: $link";
    }

    try {
        symlink($target, $link);
        return "Symbolic link created successfully: $link → $target";
    } catch (\Exception $e) {
        return "Failed to create symbolic link: " . $e->getMessage();
    }
});

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('setup/areas', Areas::class)->name('setup.areas')->middleware('can:view-area');
    Route::get('setup/check-methods', CheckMethods::class)->name('setup.check-methods')->middleware('can:view-check-method');
    Route::get('setup/check-items', CheckItems::class)->name('setup.check-items')->middleware('can:view-check-item');

    Route::get('daily/daily-check-matrix', DailyCheckMatrix::class)->name('daily.daily-check-matrix')->middleware('can:view-daily-check');
    Route::get('daily/daily-check/{id?}', DailyCheckForm::class)->name('daily.daily-check')->middleware('can:view-daily-check');
    // Route::get('daily/daily-check/{id?}', DailyCheckForm::class)
    // ->name('daily.daily-check')
    // ->middleware('signed');

    Route::get('daily/upload-photo', UploadPhoto::class)->name('daily.upload-photo');

    Route::get('/machine-report', MachineInspectionReport::class)->name('machine.report');

    Route::get('users/manage', UsersManage::class)->name('users.manage')->middleware('can:view-users');
    Route::get('users/assign-roles', AssignRoles::class)->name('users.assign-roles')->middleware('can:view-users');

    Route::middleware(['can:admin-only'])->group(function () {
        Route::get('roles/manage', Manage::class)->name('roles.manage');
        Route::get('permissions/manage', PermissionsManage::class)->name('permissions.manage');
    });

});

require __DIR__ . '/auth.php';
