<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\UsersController;

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

//public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products', function() {
    return 'products';
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

//TODO: uncoment to using!
//protected routes
Route::post('/passwordUpdate', [AuthController::class, 'updatePassword']);
Route::post('/profileUpdate', [AuthController::class, 'profileUpdate']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/users', [UsersController::class, 'index']);
Route::post('/category/new', [CategoryController::class, 'store']);
Route::group(['middleware' => ['auth:passport']], function() {
    Route::get('user/{email}', [AuthController::class, 'userDetail']);
    // Route::get('/products', [ProductsController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);
    


// Route::group(['prefix' => 'users'], function() {
//     Route::get('/users', 'UsersController@index')->name('users.index');
//     Route::get('/create', 'UsersController@create')->name('users.create');
//     Route::post('/create', 'UsersController@store')->name('users.store');
//     Route::get('/{user}/show', 'UsersController@show')->name('users.show');
//     Route::get('/{user}/edit', 'UsersController@edit')->name('users.edit');
//     Route::patch('/{user}/update', 'UsersController@update')->name('users.update');
//     Route::delete('/{user}/delete', 'UsersController@destroy')->name('users.destroy');
//     });

Route::resource('roles', RolesController::class);
Route::resource('permissions', PermissionsController::class);
});