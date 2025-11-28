<?php

use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RestaurantController::class, 'map'])->name('home');
Route::get('/map', [RestaurantController::class, 'map'])->name('restaurants.map');

Route::prefix('restaurants')->group(function () {
    Route::get('/', [RestaurantController::class, 'index'])->name('restaurants.index');
    Route::get('/search', [RestaurantController::class, 'search'])->name('restaurants.search');
    Route::get('/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/', [RestaurantController::class, 'store'])->name('restaurants.store');
    Route::get('/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');
});

Route::prefix('reviews')->group(function () {
    Route::post('/{restaurantId}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/{reviewId}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});


Route::get('/api/restaurants', [RestaurantController::class, 'apiRestaurants'])->name('api.restaurants');