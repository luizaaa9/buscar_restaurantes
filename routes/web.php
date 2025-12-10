<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return redirect()->route('restaurants.map');
});

Route::get('/restaurants/map', [RestaurantController::class, 'map'])->name('restaurants.map');
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/search', [RestaurantController::class, 'search'])->name('restaurants.search');
Route::get('/restaurants/create', [RestaurantController::class, 'create'])->name('restaurants.create');
Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::get('/restaurants/{id}/edit', [RestaurantController::class, 'edit'])->name('restaurants.edit');
Route::put('/restaurants/{id}', [RestaurantController::class, 'update'])->name('restaurants.update');
Route::delete('/restaurants/{id}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');

Route::post('/restaurants/{restaurantId}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::delete('/reviews/{reviewId}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});


Route::post('/update-location', [RestaurantController::class, 'updateUserLocation'])->name('update.location');
Route::get('/api/nearby-restaurants', [RestaurantController::class, 'nearbyRestaurants'])->name('api.nearby.restaurants');
Route::post('/api/nearby-restaurants', [RestaurantController::class, 'nearbyRestaurantsPost'])->name('api.nearby.restaurants.post');


Route::fallback(function () {
    return redirect()->route('restaurants.map');
});