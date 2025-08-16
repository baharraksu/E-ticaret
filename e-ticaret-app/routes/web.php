<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MongoDBTestController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [HomeController::class, 'products'])->name('products');
Route::get('/product/{id}', [HomeController::class, 'product'])->name('product');

// MongoDB Test Rotaları
Route::prefix('mongodb')->group(function () {
    Route::get('/dashboard', [MongoDBTestController::class, 'dashboard'])->name('mongodb.dashboard');
    Route::get('/test-connection', [MongoDBTestController::class, 'testConnection'])->name('mongodb.test-connection');
    Route::get('/insert-test-data', [MongoDBTestController::class, 'insertTestData'])->name('mongodb.insert-test-data');
    Route::get('/list-test-data', [MongoDBTestController::class, 'listTestData'])->name('mongodb.list-test-data');
    Route::get('/list-collections', [MongoDBTestController::class, 'listCollections'])->name('mongodb.list-collections');
});

// Sepet işlemleri
Route::prefix('cart')->group(function () {
    Route::get('/', function () {
        return view('cart');
    })->name('cart');
    
    Route::post('/add', function () {
        // Sepete ekleme işlemi
    })->name('cart.add');
    
    Route::delete('/remove/{id}', function () {
        // Sepetten çıkarma işlemi
    })->name('cart.remove');
});
