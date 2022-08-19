<?php

use App\Http\Controllers\authController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//Refresh token 



// then give permission for login 
Route::post('register', [authController::class, 'register_post']);



// if any response come check token is valid or not 
// if not return the new token with 5 min expiry date

Route::post('login', [authController::class, 'login_post']);


Route::middleware('auth:sanctum')->group(function ()
{

    Route::get("test",[authController::class, 'index']);
    
    Route::post('token', [authController::class, 'checkValidity']);
    
    
});


// logout system after token expired
// re generate token
// assign new token to user 
