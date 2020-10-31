<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('countries', [App\Http\Controllers\Api\MasterCountroller::class, 'CountryList']);
Route::get('news-paper/{id?}', [App\Http\Controllers\Api\MasterCountroller::class, 'NewsPaperList']);
Route::get('news-youtube/{id?}', [App\Http\Controllers\Api\MasterCountroller::class, 'NewsYoutubeList']);
