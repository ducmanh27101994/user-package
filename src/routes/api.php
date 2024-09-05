<?php

use Illuminate\Support\Facades\Route;
use FmcExample\UserPackage\Http\Controllers\AuthController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
