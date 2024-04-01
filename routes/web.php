<?php

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
Route::get('', function () {
    return view('app');
});

Route::get('/{page}', function () {
    return view('app');
});
Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->name('password.reset');

Route::middleware(['auth:sanctum'])->group(function (){
    Route::post('/events/{event}/subscribe', [UserController::class, 'event_subscribe']);
    Route::post('/events/{event}/unsubscribe', [UserController::class, 'event_unsubscribe']);

});
