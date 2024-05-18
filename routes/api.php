<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
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

Route::get('/events', [EventController::class, 'index']);
Route::get('/countries', [CountryController::class, 'countries_filter']);
Route::get('/calendar/events', [EventController::class, 'calendar_index']);
Route::get('/addresses', [AddressController::class, 'index']);
Route::get('/characters', [CharacterController::class, 'index']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/forgot-password', [UserController::class, 'forgot_password']);
Route::post('/reset-password', [UserController::class, 'reset_password']);
Route::get('/events/statistics', [EventController::class, 'get_statistics']);


Route::middleware(['auth:sanctum'])->group(function (){
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/events/{event}/subscribe', [EventController::class, 'event_subscribe']);
    Route::post('/events/{event}/unsubscribe', [EventController::class, 'event_unsubscribe']);
    Route::get('/users/{user}/check-authentication', [UserController::class, 'check_authentication']);
    Route::get('/users/{user}/profile', [UserController::class, 'get_profile']);
    Route::middleware(['check-authentication'])->group(function(){
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::get('/users/{user}/notifications/count', [NotificationController::class, 'get_notifications_count']);
        Route::get('/users/{user}/notifications', [NotificationController::class, 'get_notifications']);
        Route::get('/users/{user}/settings', [SettingsController::class, 'get_settings']);
        Route::put('/users/{user}/settings', [SettingsController::class, 'update_settings']);
        Route::put('/users/{user}/settings/update-distance-notifications-radius', [SettingsController::class, 'update_distance_notifications_radius']);
        Route::put('/users/{user}/profile/', [UserController::class, 'update_profile']);
    });



});
