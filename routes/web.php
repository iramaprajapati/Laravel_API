<?php

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

/* My Test Code
Route::get('/{name?}', function ($name = null) {
    $data = compact('name');
    return view('home_page')->with($data);
});
Route::get('/laravel', function () {
    return view('welcome');
});
*/

Route::get('/', function () {
    return view('welcome');
});
