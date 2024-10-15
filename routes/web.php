<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\TermController;

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

    Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);


    Route::resource('terms', Admin\TermController::class)->only(['index', 'edit', 'update']);

    Route::get('companies', [Admin\CompanyController::class, 'index'])->name('company.index'); // 新しいルートを追加

    Route::get('companies/{company}/edit', [Admin\CompanyController::class, 'edit'])->name('company.edit');

    Route::patch('companies/{company}/update', [Admin\CompanyController::class, 'update'])->name('company.update');
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

