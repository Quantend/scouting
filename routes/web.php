<?php

use App\Livewire\AppSettingsComponent;
use App\Livewire\HoursComponent;
use App\Livewire\LogsComponent;
use App\Livewire\TaskComponent;
use App\Livewire\MemberComponent;
use App\Livewire\AdminComponent;
use App\Livewire\ViewHoursComponent;
use App\Livewire\ChartComponent;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::group(['middleware' => function ($request, $next) {
    if (!Auth::check() || (Auth::user()->is_admin !== 1 || Auth::user()->is_deleted === 1)) {
        abort(403, 'Unauthorized access');
    }
    return $next($request);
}], function () {
    Route::get('members', MemberComponent::class)->name('members');
    Route::get('tasks', TaskComponent::class)->name('tasks');
    Route::get('hours', HoursComponent::class)->name('hours');
    Route::get('view-hours', ViewHoursComponent::class)->name('view-hours');
    Route::get('charts', ChartComponent::class)->name('charts');
});

Route::group(['middleware' => function ($request, $next) {
    if (!Auth::check() || (Auth::user()->is_super_admin !== 1 || Auth::user()->is_deleted === 1)) {
        return redirect()->route('dashboard');
    }
    return $next($request);
}], function () {
    Route::get('admin', AdminComponent::class)->name('admin');
    Route::get('logs', LogsComponent::class)->name('logs');
    Route::get('app-settings', AppSettingsComponent::class)->name('app-settings');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
