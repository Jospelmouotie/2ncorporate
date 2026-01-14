<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});// routes/web.php


// Cette route doit être à la toute fin du fichier
Route::get('/{any}', function () {
    return view('welcome'); // ou le nom de votre blade principal
})->where('any', '.*');
