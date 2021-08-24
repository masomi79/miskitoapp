<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('app.main');
    // return redirect('/dic');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/dic', function(){
    return view('app.main');
});

// ログインチェック
Route::get('/data/loginCheck', [AdminController::class, 'loginCheck']);
// 収録語数
Route::get('/data/getTotalNumber', [AdminController::class, 'getTotalNumber']);
// 入力された言語を判定
Route::post('/data/checkWordsLang', [AdminController::class, 'checkWordsLang']);
// 検索結果
Route::post('/data/getSearchResult', [AdminController::class, 'getSearchResult']);
// 再検索結果
Route::post('/data/getSearchResultById', [AdminController::class, 'getSearchResultById']);
// 新単語の登録
Route::post('/data/registerNewWord', [AdminController::class, 'registerNewWord']);
// 単語の種類を登録
Route::post('/data/setWordType', [AdminController::class, 'setWordType']);
// 関連語句を表示
Route::post('/data/getRelatedTerms', [AdminController::class, 'getRelatedTerms']);
// 提案語句を表示
Route::post('/data/getSuggestions', [AdminController::class, 'getSuggestions']);
// 例文を表示
//Route::post('/data/getExamples', [AdminController::class, 'getExamples']);
// 例文を登録
Route::post('/data/registerNewExample', [AdminController::class, 'registerNewExample']);
// 関係を削除
Route::post('/data/deleteRelation', [AdminController::class, 'deleteRelation']);
// 単語の存在のチェック
Route::post('/data/wordCheck', [AdminController::class, 'wordCheck']);



Route::get('/{any}', function () {
    return view('app.main');
    // return redirect('/dic');
});