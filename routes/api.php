<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\EventController;
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

//Route::get('/timezone', [Controller::class, 'getVisitorTimezone']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/countries', [CountryController::class, 'countries_filter']);
Route::get('/calendar/events', [EventController::class, 'calendar_index']);
Route::get('/addresses', [AddressController::class, 'index']);
Route::get('/characters', [CharacterController::class, 'index']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/forgot-password', [UserController::class, 'forgot_password']);
Route::post('/reset-password', [UserController::class, 'reset_password']);

Route::middleware(['auth:sanctum'])->group(function (){
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/events/{event}/subscribe', [UserController::class, 'event_subscribe']);
    Route::post('/events/{event}/unsubscribe', [UserController::class, 'event_unsubscribe']);
});
