<?php

use App\Http\Controllers\DefenderController;
use App\Http\Controllers\ReportController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth.token', 'auth.capability'])
->name('api.')
->group(function()
{
    Route::prefix('v1')
    ->name('v1.')
    ->group(function()
    {
        Route::prefix('defender')
        ->name('defender.')
        ->group(function()
        {
            Route::get('list', [DefenderController::class, 'list'])->name('list');
            Route::get('show/{id}', [DefenderController::class, 'show'])->name('show');
            Route::post('create', [DefenderController::class, 'create'])->name('create');
            Route::patch('update/{id}', [])->name('update');
            Route::delete('delete/{id}', [])->name('delete');
        });
    });
});

Route::middleware('auth.defender.report')
->post('/report/create', [ReportController::class, 'create']);