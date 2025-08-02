<?php

use App\Http\Controllers\DecisionController;
use App\Http\Controllers\DefenderController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PermissionController;
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
        Route::prefix('decisions')
        ->name('decisions.')
        ->group(function()
        {
            Route::get('list', [DecisionController::class, 'list'])->name('list');
            Route::get('show/{id}', [DecisionController::class, 'show'])->name('show');
            Route::post('create', [DecisionController::class, 'create'])->name('create');
            Route::patch('update/{id}', [DecisionController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [DecisionController::class, 'delete'])->name('delete');
        });

        Route::prefix('defenders')
        ->name('defenders.')
        ->group(function()
        {
            Route::get('list', [DefenderController::class, 'list'])->name('list');
            Route::get('show/{id}', [DefenderController::class, 'show'])->name('show');
            Route::post('create', [DefenderController::class, 'create'])->name('create');
            Route::patch('update/{id}', [DefenderController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [DefenderController::class, 'delete'])->name('delete');
        });

        Route::prefix('fingerprints')
        ->name('fingerprints.')
        ->group(function()
        {
            Route::get('list', [FingerprintController::class, 'list'])->name('list');
            Route::get('show/{id}', [FingerprintController::class, 'show'])->name('show');
            Route::delete('delete/{id}', [FingerprintController::class, 'delete'])->name('delete');
        });

        Route::prefix('groups')
        ->name('groups.')
        ->group(function()
        {
            Route::get('list', [GroupController::class, 'list'])->name('list');
            Route::get('show/{id}', [GroupController::class, 'show'])->name('show');
            Route::post('create', [GroupController::class, 'create'])->name('create');
            Route::patch('update/{id}', [GroupController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [GroupController::class, 'delete'])->name('delete');
        });

        Route::prefix('permissions')
        ->name('permissions.')
        ->group(function()
        {
            Route::get('list', [PermissionController::class, 'list'])->name('list');
            Route::get('show/{id}', [PermissionController::class, 'show'])->name('show');
            Route::post('create', [PermissionController::class, 'create'])->name('create');
            Route::patch('update/{id}', [PermissionController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [PermissionController::class, 'delete'])->name('delete');
        });
    });
});

Route::middleware('auth.defender.report')
->post('/report/create', [ReportController::class, 'create']);