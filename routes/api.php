<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['cors'])->group(function () {
    Route::post('upload', 'App\Http\Controllers\UploadController@upload');
    Route::get('check-jobs', 'App\Http\Controllers\GithubController@checkJobs');

    Route::post('deploy', 'App\Http\Controllers\GithubController@deploy');

    Route::get('recent-apk/{client}', 'App\Http\Controllers\ClientController@getRecentApk');
    Route::get('profile/{client}', 'App\Http\Controllers\ClientController@getProfile');

    Route::get('download/{file}', 'App\Http\Controllers\DownloadController@download');
    Route::get('download/client/{client}', 'App\Http\Controllers\DownloadController@downloadClient');

    Route::get('clients', 'App\Http\Controllers\ClientController@getClients');
    Route::post('client', 'App\Http\Controllers\ClientController@createClient');
    Route::patch('client/{username}', 'App\Http\Controllers\ClientController@editClient');
    Route::delete('client/{username}', 'App\Http\Controllers\ClientController@deleteClient');

    Route::get('splashscreen', 'App\Http\Controllers\SplashController@getSplash');
    Route::post('splashscreen', 'App\Http\Controllers\SplashController@createSplash');

    Route::post('icon', 'App\Http\Controllers\ClientController@changeIcon');

    Route::get('notifications/{client}', 'App\Http\Controllers\NotificationController@all');
    Route::get('notification/{client}', 'App\Http\Controllers\NotificationController@index');
    Route::post('notification', 'App\Http\Controllers\NotificationController@create');

    Route::post('v2/upload', 'App\Http\Controllers\UploadController@uploadLargeFiles');
});
