<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\PendingController;
use App\Http\Controllers\Api\DebtController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\DashboardController;

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



Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/checklogin', [AuthController::class, 'me']);
    Route::post('/change_pwd', [AuthController::class, 'change_pwd']);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('pending', PendingController::class);
    Route::post('/pending/{pending}/pay', [PendingController::class, 'pay']);    
    Route::apiResource('debts', DebtController::class);
    Route::post('/debts/{debt}/pay', [DebtController::class, 'pay']);

    Route::get('/stats/top_clients', [StatsController::class, 'top_clients']);
    Route::get('/stats/amount_by_all_years', [StatsController::class, 'amount_by_all_years']);
    Route::get('/stats/amount_by_year', [StatsController::class, 'amount_by_year']);
    Route::get('/stats/amount_by_client', [StatsController::class, 'amount_by_client']);

    Route::get('/dashboard/text_widget', [DashboardController::class, 'text_widget']);
    Route::get('/dashboard/mounthly_graph', [DashboardController::class, 'mounthly_graph']);
});