<?php

use App\Http\Controllers\DefenderController;
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

Route::get('/', fn() => response()->json(['message' => 'connected']));

Route::name('manager.')
->prefix(env('MANAGER_PATH_PREFIX', 'manager'))
->group(function ()
{
    Route::get("verification/{token}", [UserController::class, 'verify'])->name('verification');
    Route::middleware(['auth' ,'auth.defender.collect'])
    ->get("collection/{id}", [DefenderController::class, 'collect'])->name('collection');
});
