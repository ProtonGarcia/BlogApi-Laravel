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

Route::get('/pruebas','TestController@index');


/**
 * Pasar parametros por la rutas
 * 
 * para ser opcional necesita un signo ?
 * y en la funcion de callback igualarla a null
 */

Route::get('/test/{param}', function ($param) {
    $texto = 'probando los parametros ';
    $texto .= 'parametro por url: ' . $param;

    return view('test', array(
        'text' => $texto
    ));
});


Route::get('/test-orm', 'TestController@testOrm');