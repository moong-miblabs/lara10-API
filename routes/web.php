<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', fn() => env('APP_NAME'));

use App\Http\Controllers\DatabaseController;
Route::controller(DatabaseController::class)->prefix('database')->group(function () {
    Route::get('/create', 'create');
    Route::get('/store', 'store');
    Route::get('/destroy', 'destroy');
});
