<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController; // Importing UserController

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
/* 
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// My old API methods test routes
Route::get("/user", function () {
    return "Hello User....";
});

Route::post('/user', function () {
    return response()->json("Post API Hit Successfully....");
});

Route::delete("/user/{id}", function ($id) {
    //return "Delete user with id = ". $id;
    return ["result" => ["message" => "User Deleted Successfully"]];
})->where('id', '[0-9]+');

Route::put("/user/{id}", function ($id) {
    return ["result" => ["message" => "User Updated Successfully", "userId" => $id]];
})->where('id', '[0-9]+');
*/

/*
// To use any controller method, we need to use the following syntax
// 1. Namespace
// 2. Class
// 3. Method

// Both are correct ways:-
App\Http\Controllers\Api\UserController@store
[UserController::class,'index']
*/

Route::get('/users/get/{flag}', [UserController::class, 'index']);
Route::get('/user/{id}', [UserController::class, 'show']);
Route::post('user/store',  [UserController::class, 'store']);
Route::put('user/update/{id}',  [UserController::class, 'update']);
Route::delete('/user/delete/{id}', [UserController::class, 'destroy']);
Route::patch('/user/change-password/{id}', [UserController::class, 'changePassword']);

Route::post('user/register',  [UserController::class, 'userRegister']);
