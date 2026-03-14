<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/parts', [PartController::class, 'index'])->name('parts.index');
Route::get('/parts/{part:slug}', [PartController::class, 'show'])->name('parts.show');

Route::get('/now-dismantling', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/now-dismantling/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit')->middleware('throttle:5,1');

Route::post('/enquire', [PartController::class, 'enquire'])->name('parts.enquire')->middleware('throttle:10,1');

/*
| Mini-app: add parts and vehicles from phone (PIN-protected)
*/
Route::prefix('app')->name('app.')->middleware(['web', 'miniapp.auth'])->group(function () {
    Route::get('login', [\App\Http\Controllers\MiniappController::class, 'login'])->name('login')->withoutMiddleware('miniapp.auth');
    Route::post('login', [\App\Http\Controllers\MiniappController::class, 'loginPost'])->name('login.post')->withoutMiddleware('miniapp.auth');
    Route::get('logout', [\App\Http\Controllers\MiniappController::class, 'logout'])->name('logout');
    Route::get('/', [\App\Http\Controllers\MiniappController::class, 'dashboard'])->name('dashboard');
    Route::get('categories/{category}/subcategories', [\App\Http\Controllers\MiniappController::class, 'subcategories'])->name('subcategories');
    Route::get('parts/new', [\App\Http\Controllers\MiniappController::class, 'createPart'])->name('parts.create');
    Route::post('parts', [\App\Http\Controllers\MiniappController::class, 'storePart'])->name('parts.store');
    Route::get('vehicles/new', [\App\Http\Controllers\MiniappController::class, 'createVehicle'])->name('vehicles.create');
    Route::post('vehicles', [\App\Http\Controllers\MiniappController::class, 'storeVehicle'])->name('vehicles.store');
});
