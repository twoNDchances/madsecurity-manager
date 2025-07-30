<?php

use App\Http\Controllers\CollectController;
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
    $verificationPrefix = env('MANAGER_VERIFICATION_ROUTE', 'verify');
    Route::get("/$verificationPrefix/{token}", [UserController::class, 'verify'])->name('verification');

    $collectionPrefix = env('MANAGER_COLLECTION_ROUTE', 'collect');
    Route::middleware('auth.defender.collect')
    ->get($collectionPrefix, [CollectController::class, 'collect'])->name('collection');
});
