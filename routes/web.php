<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LawController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DashboardController;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verified', 'admin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('/laws')->group(function () {
        Route::controller(LawController::class)->group(function () {
            Route::get('/', 'index')->name('laws.index');
            Route::get('/{identifier}', 'show')->name('laws.show');
            Route::put('/{identifier}', 'update')->middleware('admin')->name('laws.update');
        });
    });

    Route::get('/documents/download/{filename}', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/show/{filename}', [DocumentController::class, 'show'])->name('documents.show');
});

require __DIR__.'/auth.php';
