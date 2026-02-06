<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// On ajoute une condition pour NE PAS capturer les routes API
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$'); // Regex qui exclut 'api'
