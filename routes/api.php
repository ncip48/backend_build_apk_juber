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

Route::post('upload', 'App\Http\Controllers\UploadController@upload');
Route::get('check-jobs', 'App\Http\Controllers\GithubController@checkJobs');

Route::post('deploy', 'App\Http\Controllers\GithubController@deploy');

Route::get('recent-apk/{client}', 'App\Http\Controllers\ClientController@getRecentApk');
Route::get('profile/{client}', 'App\Http\Controllers\ClientController@getProfile');

Route::get('download/{file}', 'App\Http\Controllers\DownloadController@download');
