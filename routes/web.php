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
