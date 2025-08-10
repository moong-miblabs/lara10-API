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

use App\Http\Controllers\DeviceController;
Route::controller(DeviceController::class)->prefix('device')->group(function () {
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

use App\Http\Controllers\MasterToSlave;
Route::controller(MasterToSlave::class)->prefix('mts')->group(function () {
    Route::get('/','see');
    Route::post('/','turn_on');
    Route::get('/off','turn_off');
});

use App\Http\Controllers\AudioController;
Route::controller(AudioController::class)->prefix('audio')->group(function () {
    Route::get('/','index');
    Route::get('/{id}','show');
    Route::post('/','create');
    Route::patch('/{id}','edit');
    Route::delete('/{id}','destroy');
});