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

Route::get('/', function () {
    return view('welcome');
});

Route::name('manager.')
->group(function ()
{
    $prefix = env('MANAGER_VERIFICATION_ROUTE', 'verify');
    Route::get("/$prefix/{token}", [UserController::class, 'verify'])->name('verification');
});
