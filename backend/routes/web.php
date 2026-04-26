<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-auth', function () {
    return file_get_contents(public_path('test-auth.html'));
});
