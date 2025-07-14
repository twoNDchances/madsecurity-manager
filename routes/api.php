<?php

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

Route::middleware([])
->name('api')
->group(function()
{
    Route::prefix('report')
    ->name('report')
    ->group(function()
    {
        Route::get('/list', []);
        Route::delete('/delete', []);
    });
});


Route::middleware('auth.defender.report')
->post('/report/create', [ReportController::class, 'create']);