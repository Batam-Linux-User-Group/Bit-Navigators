<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/monyong', function () {
    return view('monyong');
});

Route::get('/1seed', function () {
    return view('1seed');
});
