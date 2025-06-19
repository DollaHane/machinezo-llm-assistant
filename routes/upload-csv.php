<?php

use App\Http\Controllers\Upload\UploadController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
  Route::post('upload-csv', [UploadController::class, 'create']);
});