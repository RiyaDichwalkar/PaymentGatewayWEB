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

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home')->middleware('verified');
Route::post('paypal/express-checkout', 'PaypalController@expressCheckout')->name('paypal.express-checkout');
Route::get('paypal/express-checkout-success', 'PaypalController@expressCheckoutSuccess');
Route::post('paypal/notify', 'PaypalController@notify');
// Route::get('/receipt',function () {
//   return view('receipt');
// });
Route::get('/receipt','DynamicPDFController@index');
Route::get('/receipt/pdf', 'DynamicPDFController@pdf');
Route::get('/receipt/email', 'DynamicPDFController@send_email');
// Route::get('profile', function () {
//   return 'This is Profile ';
// })->middleware('verified');