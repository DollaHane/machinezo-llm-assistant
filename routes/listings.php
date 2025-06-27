<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Listings\ListingsController;

Route::middleware('auth')->group(function () {
  Route::get('/listings', [ListingsController::class, 'index'])->name('listings.index');
  Route::get('/update/{id}', [ListingsController::class, 'show'])->name('listings.show');
  Route::post('/listings', [ListingsController::class, 'create']);
  Route::put('/listings', [ListingsController::class, 'update'])->name('listings.update');
  Route::delete('/listings/{id}', [ListingsController::class, 'delete'])->name('listings.delete');

  Route::prefix('V2')->group(function () {
    Route::post('/listings', [ListingsController::class, 'createV2']);
    Route::put('/listings', [ListingsController::class, 'updateV2'])->name('listings.updateV2');
  });
});
