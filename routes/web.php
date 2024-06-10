<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
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

#TODO Ask Quentin if it is faster to query items in a store with the home made api or to redirect to the page with the items inside index (si index a été chargé 1x et query api si non).

Route::get('/crons/import-500-events', [EventController::class, 'import_500_events']);
Route::get('/crons/delete-events', [EventController::class, 'delete_events']);
Route::get('/crons/delete-addresses', [AddressController::class, 'delete_addresses']);

Route::get('', function () {
    return view('app');
});

Route::get('/{page}', function () {
    return view('app');
});

Route::get('/{page}/{token}', function () {
    return view('app');
})->name('password.reset');

// This route is used to reset the password (needed for PasswordBroker)
Route::get('/reset-password/{token}', function (string $token) {})->name('password.reset');

