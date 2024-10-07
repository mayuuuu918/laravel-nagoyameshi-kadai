<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



require __DIR__.'/auth.php';

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth:admin'], function () {
    Route::get('home', [Admin\HomeController::class, 'index'])->name('home');

    Route::resource('users', Admin\UserController::class)->only(['index', 'show']);

    Route::resource('restaurants', Admin\RestaurantController::class);
});

/*
Route::prefix('admin')->group(function () {
    // ユーザー一覧ページのルート
    Route::get('users', [UserController::class, 'index'])->middleware('auth:admin')->name('admin.users.index');

    // ユーザー詳細ページのルート
    Route::get('users/{user}', [UserController::class, 'show'])->middleware('auth:admin')->name('admin.users.show');
});


Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::resource('users', UserController::class);
});
*/

