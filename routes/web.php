<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home-v2');
})->name('home');

Route::get('/home-v2', function () {
    return view('home-v2');
})->name('home.v2');

Route::get('/parts', [PartController::class, 'index'])->name('parts.index');
Route::get('/parts/{part:slug}', [PartController::class, 'show'])->name('parts.show');

Route::get('/now-dismantling', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/now-dismantling/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');

Route::get('/feeds/google.xml', [\App\Http\Controllers\FeedController::class, 'google'])->name('feeds.google');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit')->middleware('throttle:5,1');

Route::post('/enquire', [PartController::class, 'enquire'])->name('parts.enquire')->middleware('throttle:10,1');

/*
| Reserve for collection — the purchase path Google requires before it will
| show products. Payment happens on collection.
*/
Route::post('/parts/{part:slug}/reserve', [\App\Http\Controllers\ReservationController::class, 'store'])
    ->name('reservations.store')
    ->middleware('throttle:10,1');
Route::get('/reservations/{reference}', [\App\Http\Controllers\ReservationController::class, 'show'])
    ->name('reservations.show');

/*
| Shop: cart, checkout, Stripe return URLs and webhook.
*/
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{part:slug}', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{part}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:10,1');
Route::get('/checkout/success/{reference}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel/{reference}', [\App\Http\Controllers\CheckoutController::class, 'cancel'])->name('checkout.cancel');

// Stripe posts here server-to-server; CSRF-exempt, signature-verified instead.
Route::post('/stripe/webhook', [\App\Http\Controllers\CheckoutController::class, 'webhook'])->name('stripe.webhook');

// Policy pages Google checks for. Whitelisted rather than a catch-all so
// unknown URLs still 404 properly.
Route::get('/{page:key}', [PageController::class, 'show'])
    ->where('page', 'returns-policy|shipping-policy|terms-and-conditions')
    ->name('pages.show');

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
    Route::get('stock', [\App\Http\Controllers\MiniappController::class, 'stock'])->name('stock');
    Route::get('orders', [\App\Http\Controllers\MiniappController::class, 'orders'])->name('orders');
    Route::post('orders/{order}/status', [\App\Http\Controllers\MiniappController::class, 'updateOrderStatus'])->name('orders.status');
    Route::get('reservations', [\App\Http\Controllers\MiniappController::class, 'reservations'])->name('reservations');
    Route::post('reservations/{reservation}/status', [\App\Http\Controllers\MiniappController::class, 'updateReservationStatus'])->name('reservations.status');
    Route::post('stock/{part}/status', [\App\Http\Controllers\MiniappController::class, 'updatePartStatus'])->name('stock.status');
    Route::get('vehicles/new', [\App\Http\Controllers\MiniappController::class, 'createVehicle'])->name('vehicles.create');
    Route::post('vehicles', [\App\Http\Controllers\MiniappController::class, 'storeVehicle'])->name('vehicles.store');
});
