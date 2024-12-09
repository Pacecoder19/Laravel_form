<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

/*
|----------------------------------------------------------------------
| Web Routes
|----------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| Routes are loaded by the RouteServiceProvider and are assigned to
| the "web" middleware group.
|
*/

Route::get('/', function () {
    return view('laravel_form');
});

Route::post('/uploadform', [FormController::class, 'upload']);
Route::get('/get-users', [FormController::class, 'getUsers']);
