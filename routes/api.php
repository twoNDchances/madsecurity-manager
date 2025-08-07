<?php

use App\Http\Controllers\DecisionController;
use App\Http\Controllers\DefenderController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WordlistController;
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
            Route::post('health/{id}', [DefenderController::class, 'health'])->name('health');
            Route::post('collect/{id}', [DefenderController::class, 'collect'])->name('collect');
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

        Route::prefix('policies')
        ->name('policies.')
        ->group(function()
        {
            Route::get('list', [PolicyController::class, 'list'])->name('list');
            Route::get('show/{id}', [PolicyController::class, 'show'])->name('show');
            Route::post('create', [PolicyController::class, 'create'])->name('create');
            Route::patch('update/{id}', [PolicyController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [PolicyController::class, 'delete'])->name('delete');
        });

        Route::prefix('rules')
        ->name('rules.')
        ->group(function()
        {
            Route::get('list', [RuleController::class, 'list'])->name('list');
            Route::get('show/{id}', [RuleController::class, 'show'])->name('show');
            Route::post('create', [RuleController::class, 'create'])->name('create');
            Route::patch('update/{id}', [RuleController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [RuleController::class, 'delete'])->name('delete');
        });

        Route::prefix('tags')
        ->name('tags.')
        ->group(function()
        {
            Route::get('list', [TagController::class, 'list'])->name('list');
            Route::get('show/{id}', [TagController::class, 'show'])->name('show');
            Route::post('create', [TagController::class, 'create'])->name('create');
            Route::patch('update/{id}', [TagController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [TagController::class, 'delete'])->name('delete');
        });

        Route::prefix('targets')
        ->name('targets.')
        ->group(function()
        {
            Route::get('list', [TargetController::class, 'list'])->name('list');
            Route::get('show/{id}', [TargetController::class, 'show'])->name('show');
            Route::post('create', [TargetController::class, 'create'])->name('create');
            Route::patch('update/{id}', [TargetController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [TargetController::class, 'delete'])->name('delete');
        });

        Route::prefix('tokens')
        ->name('tokens.')
        ->group(function()
        {
            Route::get('list', [TokenController::class, 'list'])->name('list');
            Route::get('show/{id}', [TokenController::class, 'show'])->name('show');
            Route::post('create', [TokenController::class, 'create'])->name('create');
            Route::patch('update/{id}', [TokenController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [TokenController::class, 'delete'])->name('delete');
        });

        Route::prefix('users')
        ->name('users.')
        ->group(function()
        {
            Route::get('list', [UserController::class, 'list'])->name('list');
            Route::get('show/{id}', [UserController::class, 'show'])->name('show');
            Route::post('create', [UserController::class, 'create'])->name('create');
            Route::patch('update/{id}', [UserController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [UserController::class, 'delete'])->name('delete');
        });

        Route::prefix('wordlists')
        ->name('wordlists.')
        ->group(function()
        {
            Route::get('list', [WordlistController::class, 'list'])->name('list');
            Route::get('show/{id}', [WordlistController::class, 'show'])->name('show');
            Route::post('create', [WordlistController::class, 'create'])->name('create');
            Route::patch('update/{id}', [WordlistController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [WordlistController::class, 'delete'])->name('delete');
        });
    });
});

Route::middleware('auth.defender.report')
->post('/report/create', [ReportController::class, 'create']);