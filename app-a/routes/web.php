<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/version', function () {
    return env('APP_VERSION');
});


Route::get('/users', function () {
    $users = User::select('name')->get();
    return $users;
});