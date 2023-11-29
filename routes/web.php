<?php

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\MyAutoController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get("my_autos",[MyAutoController::class,"index"]);
Route::get('/currency', [CurrencyController::class, 'getExchangeRates']);
Route::get('/filtered', [CurrencyController::class, 'getFilteredExchangeRates']);
Route::get('/currency/{id}', [CurrencyController::class, 'show']);