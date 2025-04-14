<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LayerGroupController;
use App\Http\Controllers\LahanController;


use App\Http\Controllers\KualitasLahanController;
use App\Http\Controllers\KarakteristikLahanController;
use App\Http\Controllers\NilaiPilihanController;
use App\Http\Controllers\KarakteristikAngkaController;
use App\Http\Controllers\KarakteristikPilihanController;
use App\Http\Controllers\BasemapController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WilayahController;








// Public routes 

Route::post('/login', [AuthController::class, 'login']);

// Public GET layer group
Route::get('/layer-groups', [LayerGroupController::class, 'index']);
Route::get('/layer-groups/{id}', [LayerGroupController::class, 'show']);
Route::get('/layer-groups/user/{username}', [LayerGroupController::class, 'byUsername']);


Route::get('/lahan/{id}', [LahanController::class, 'show']); 
Route::get('/lahan/layer-group/{id_layer_groups}', [LahanController::class, 'getByLayerGroup']); 


// Public Routes (GET)
Route::get('/kualitas-lahan', [KualitasLahanController::class, 'index']);
Route::get('/kualitas-lahan/{id}', [KualitasLahanController::class, 'show']);

Route::get('/karakteristik-lahan', [KarakteristikLahanController::class, 'index']);
Route::get('/karakteristik-lahan/{id}', [KarakteristikLahanController::class, 'show']);

Route::get('/nilai-pilihan', [NilaiPilihanController::class, 'index']);
Route::get('/nilai-pilihan/karakteristik/{id}', [NilaiPilihanController::class, 'getByKarakteristik']);

Route::get('/karakteristik-angka', [KarakteristikAngkaController::class, 'index']);
Route::get('/karakteristik-angka/lahan/{id}', [KarakteristikAngkaController::class, 'getByLahan']);

Route::get('/karakteristik-pilihan', [KarakteristikPilihanController::class, 'index']);
Route::get('/karakteristik-pilihan/lahan/{id}', [KarakteristikPilihanController::class, 'getByLahan']);

Route::get('/basemaps', [BasemapController::class, 'index']);

Route::get('/provinsi', [WilayahController::class, 'getProvinsi']);
Route::get('/kabupaten/{id_provinsi}', [WilayahController::class, 'getKabupaten']);
Route::get('/kecamatan/{id_kabupaten}', [WilayahController::class, 'getKecamatan']);
Route::get('/desa/{id_kecamatan}', [WilayahController::class, 'getDesa']);






// Route untuk Administrator (login + role Administrator)
Route::middleware(['auth:sanctum', 'role:Administrator'])->group(function () {
    Route::post('/register-owner', [AuthController::class, 'registerOwner']);
    Route::post('/register-admin', [AuthController::class, 'registerAdmin']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::delete('/layer-groups/{id}', [LayerGroupController::class, 'destroy']);

    Route::post('/nilai-pilihan', [NilaiPilihanController::class, 'store']);
    Route::put('/nilai-pilihan/{id}', [NilaiPilihanController::class, 'update']);
    Route::delete('/nilai-pilihan/{id}', [NilaiPilihanController::class, 'destroy']);

    Route::get('/dashboard-admin', [DashboardController::class, 'index']);


   
});


Route::middleware(['auth:sanctum'])->group(function () {
   
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/lahan', [LahanController::class, 'index']);
    Route::get('/dashboard-owner', [DashboardController::class, 'owner']);
   


   
});

// Route untuk Administrator & owner
Route::middleware(['auth:sanctum', 'role:Administrator,owner'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/layer-groups/{id}', [LayerGroupController::class, 'update']);


    Route::post('/lahan', [LahanController::class, 'store']); 
    Route::put('/lahan/{id}', [LahanController::class, 'update']); 
    Route::delete('/lahan/{id}', [LahanController::class, 'destroy']); 

    Route::post('/karakteristik-angka', [KarakteristikAngkaController::class, 'store']);
    Route::put('/karakteristik-angka/{id}', [KarakteristikAngkaController::class, 'update']);
    Route::delete('/karakteristik-angka/{id}', [KarakteristikAngkaController::class, 'destroy']);

    Route::post('/karakteristik-pilihan', [KarakteristikPilihanController::class, 'store']);
    Route::put('/karakteristik-pilihan/{id}', [KarakteristikPilihanController::class, 'update']);
    Route::delete('/karakteristik-pilihan/{id}', [KarakteristikPilihanController::class, 'destroy']);
   
});
