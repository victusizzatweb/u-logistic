<?php

use App\Http\Controllers\DriverLicenseController;
use App\Http\Controllers\MyAutoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\IpListController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\TexPassportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/authUser', function (Request $request) {
    return $request->user();
});


Route::put('roles/{id}', [RoleController::class, 'update']);
// Route::get("authUser",[AuthController::class,"user"])->middleware('token');

Route::post("login" ,[AuthController::class,"login"]);
Route::post('/register',[UserController::class, 'register'])->name('user.register');
Route::post('/smsCode',[UserController::class, 'smsCode'])->name('user.code');
Route::apiResources([
  'user'=>UserController::class,
  "role"=>RoleController::class,
  "ip_list"=>IpListController::class,
  "currency"=>CurrencyController::class,
  "my_autos"=>MyAutoController::class,
  "passport"=>PassportController::class,
  "texPassport"=>TexPassportController::class,
  "driverLicense"=>DriverLicenseController::class
]);
Route::post('get_token', [UserController::class, 'get_token']);
Route::post('user/{id}', [UserController::class, 'update']);
Route::post('my_autos/{id}', [MyAutoController::class, 'update']);
Route::post('passport/{id}', [PassportController::class, 'update']);
Route::post('texPassport/{id}', [TexPassportController::class, 'update']);
// Route::get('/currency', [CurrencyController::class, 'getExchangeRates']);
// Route::get('/currency/{id}', [CurrencyController::class, 'show']);
// Route::get('/filtered', [CurrencyController::class, 'getFilteredExchangeRates']);