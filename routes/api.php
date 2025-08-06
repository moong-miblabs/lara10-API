<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AgamaController;
Route::controller(AgamaController::class)->prefix('agama')->group(function () {
    Route::get('/','index');
});

use App\Http\Controllers\RoleController;
Route::controller(RoleController::class)->prefix('role')->group(function () {
    Route::get('/','index');
});

use App\Http\Controllers\RuangController;
Route::controller(RuangController::class)->prefix('ruang')->group(function () {
    Route::get('/','index');
});

use App\Http\Controllers\UsersController;
Route::controller(UsersController::class)->prefix('users')->group(function () {
    Route::post('/login','login');
    
    Route::get('/','index');
    Route::get('/{id}','show');
    Route::post('/','create');
    Route::patch('/{id}','edit');
    Route::delete('/{id}','destroy');
});

use App\Http\Controllers\PasienController;
Route::controller(PasienController::class)->prefix('pasien')->group(function () {
    Route::get('/','index');
    Route::get('/{id}','show');
    Route::post('/','create');
    Route::patch('/{id}','edit');
    Route::delete('/{id}','destroy');
});


use App\Http\Controllers\AssesmenController;
Route::controller(AssesmenController::class)->prefix('assesmen')->group(function () {
    Route::get('/','index');
    Route::get('/{id}','show');
    Route::post('/','create');
    Route::patch('/{id}','edit');
    Route::delete('/{id}','destroy');
});