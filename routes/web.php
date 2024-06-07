<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::permanentRedirect('/', '/api/documentation/v1');
