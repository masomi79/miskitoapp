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

// IDと言語から単語を取得
Route::post('/getWordFromId', 'App\Http\Controllers\API\DicController@getWordFromId');
// 例文を表示
Route::post('/getExamples', 'App\Http\Controllers\API\DicController@getExamples');
// 意味の一覧を取得
Route::post('/getMeanings', 'App\Http\Controllers\API\DicController@getMeanings');
// Relationを削除
Route::post('/deleteRelation', 'App\Http\COntrollers\API\DIcController@deleteRelation');