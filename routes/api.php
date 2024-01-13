<?php

use App\Http\Controllers\AnnouncementsController;
use App\Http\Controllers\CoHubController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DriverLicenseController;
use App\Http\Controllers\DriverLocationController;
use App\Http\Controllers\DriverRequestController;
use App\Http\Controllers\MyAutoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\IpListController;
use App\Http\Controllers\PassportController;
use App\Http\Controllers\TexPassportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\V1\AnnouncementController;
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
Route::post("logout" ,[AuthController::class,"logout"])->middleware('auth:sanctum');
Route::post('/register',[UserController::class, 'register'])->name('user.register');
Route::apiResources([
  'user'=>UserController::class,
  "role"=>RoleController::class,
  "ip_list"=>IpListController::class,
  "currency"=>CurrencyController::class,
  "my_autos"=>MyAutoController::class,
  "passport"=>PassportController::class,
  "texPassport"=>TexPassportController::class,
  "driverLicense"=>DriverLicenseController::class,
  "comment"=>CommentController::class,
  "announcements"=>AnnouncementsController::class,
  'driver_requests'=>DriverRequestController::class,
  'driver_locations'=>DriverLocationController::class
]);
Route::get('announcem',[AnnouncementController::class ,'all']);
Route::post('get_token', [UserController::class, 'get_token']);
Route::post('smsCode', [UserController::class, 'smsCode']);
Route::get('getDriverRequestData/{id}',[DriverRequestController::class,'getDriverRequestData']);
Route::post('smscode_status', [UserController::class, 'smscode_status']);
Route::post('forget_password', [UserController::class, 'forget_password']); // parolni yangilash buyrugi
Route::post('user_status', [UserController::class, 'user_status']); // user statusni waiting qilish
Route::post('forget_password_update', [UserController::class, 'forget_password_update']); // parolni yangilash update qilish
Route::get('driver', [UserController::class, 'driver']); // Automabillar malumotlari
Route::post('user/{id}', [UserController::class, 'update']); //user update qilish 
Route::post('my_autos/{id}', [MyAutoController::class, 'update']); //Haydovchi automobili malumotini update qilish
Route::post('passport/{id}', [PassportController::class, 'update']);//Haydovchi passport malumotini update qilish
Route::post('texPassport/{id}', [TexPassportController::class, 'update']);//Haydovchi texpassport malumotini update qilish
Route::post('announcements/{id}', [AnnouncementsController::class, 'update']); // Elon ni update qilish
Route::get('customer_announcements/{id}', [AnnouncementsController::class, 'customer_announcements']);// harbir mijoz ni oz elonlarini korish buyrugi
Route::get('active_announcements/{id}', [DriverRequestController::class, 'active_announcements']);
Route::get('complate_announcements/{id}', [DriverRequestController::class, 'complate_announcements']);
Route::get('announcementsId/{id}', [AnnouncementsController::class, 'show']);
Route::get('new_announcements',[DriverRequestController::class,'new_announcements']);
Route::get('all_announcements/{id}',[DriverRequestController::class,'all_announcements']);
Route::get('acceptanceRequest/{id}',[DriverRequestController::class,'acceptanceRequest']);
Route::post('cancleRequest', [DriverRequestController::class,'CancleRequest']);
Route::get('driver_data/{id}', [DriverRequestController::class, 'driver_data']);
Route::get('confirmation_announcement/{id}',[DriverRequestController::class,'confirmation_announcement']);
Route::get('finish_announcement/{id}',[DriverRequestController::class,'finish_announcement']);
Route::get('DriverDataAnnouncement/{id}',[DriverRequestController::class,'DriverDataAnnouncement']);
Route::post('coHub',[CoHubController::class,'store']);
Route::get('/search', [AnnouncementsController::class, 'search']);
Route::get('/ordersSearch', [DriverRequestController::class, 'search']);
