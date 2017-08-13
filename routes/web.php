<?php

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/trade/offer', 'TradeController@offer')->name('trade.offer');
Route::post('/trade/post_offer', 'TradeController@postOffer')->name('trade.post_offer');
Route::get('/trade/confirm', 'TradeController@confirm')->name('trade.confirm');
Route::post('/trade/post_confirm', 'TradeController@postConfirm')->name('trade.post_confirm');
Route::get('/trade/get_transferwise_quote', 'TradeController@getTransferwiseQuote')->name('trade.get_transferwise_quote');
