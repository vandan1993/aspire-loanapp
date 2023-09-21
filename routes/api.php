<?php

use App\Http\Controllers\AdminAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;

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

Route::post('admin/register' , [AdminAuthController::class , 'registerUser']);
Route::post('user/register' , [AuthController::class , 'registerUser']);

Route::post('admin/login', [AdminAuthController::class , 'login']);

Route::post('user/login', [AuthController::class , 'login']);


Route::middleware(['auth:sanctum', 'type.admin'])->prefix('admin')->group(function () {

    Route::get('adminview', [AdminAuthController::class , 'adminview']);

    Route::post('getAllUserLoanList' , [AdminAuthController::class , 'getAllUserLoanList']);

    Route::post('getSingleUserLoan' , [AdminAuthController::class , 'getSingleUserLoan']);

    Route::post('approveSingleLoanByAdmin' ,  [AdminAuthController::class , 'approveSingleLoan']);
});

Route::middleware(['auth:sanctum', 'type.user'])->prefix('user')->group(function () {

   // Route::get('user/login', [AuthController::class , 'login']);
   Route::get('userview', [AuthController::class , 'userview']);

   Route::post('createLoan' , [LoanController::class , 'createloan']);

   Route::post('userSingleLoan' , [LoanController::class , 'userSingleLoan']);
   
   Route::post('userLoanList' , [LoanController::class , 'userLoanList']);

   Route::post('loanRepayment' , [LoanController::class , 'loanRepayment']);



});

Route::post('login' , function(){
    abort(404, 'Not Found');
});