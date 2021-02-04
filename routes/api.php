<?php

use Illuminate\Http\Request;
use App\Http\Middleware\ApiAuthMiddleware;
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

/**
 * Metodos HTTP
 * 
 * get =  conseguir datos o recursos
 * 
 * post = guardar datos o recursos
 * 
 * put o patch = actualizar datos o recursos
 * 
 * delete = eliminar datos o recursos
 * 
 * IMPORTANTE
 * una apirest rest solo hace uso de GET y POST
 * 
 * una apirestful hace uso de todos los metodos http
 */



/**
 * Rutas de prueba
 */
//Route::get('/usuario/pruebas', 'UserController@pruebas');
//Route::get('/post/pruebas', 'PostController@pruebas');
//Route::get('/categoria/pruebas', 'CategoryController@pruebas');


/**
 * USUARIOS
 */
Route::get('/user/avatar/{filename}', 'UserController@getImage');
Route::get('/user/detail/{id}', 'UserController@details');

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');

//Aplicando el middleware de auth
Route::post('/avatar', 'UserController@upload')->middleware(ApiAuthMiddleware::class);

Route::put('/update', 'UserController@update');


/**
 * CATEGORIAS
 * estas seran rutas de tipo resource 
 */
Route::resource('/category', 'CategoryController');


/**
 * POSTS
 */

Route::resource('/post', 'PostController');
//imagen del post
Route::post('/post/upload', 'PostController@upload');
Route::get('/post/image/{filename}', 'PostController@getImage');
Route::get('/post/category/{category_id}', 'PostController@getPostByCategory');
Route::get('/post/user/{user_id}', 'PostController@getPostsByUser');
