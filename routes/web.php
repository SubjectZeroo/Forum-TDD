<?php

use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('threads', [App\Http\Controllers\ThreadController::class, 'index']);

Route::get('threads/create', [App\Http\Controllers\ThreadController::class, 'create']);
Route::get('threads/{channel}/{thread}', [App\Http\Controllers\ThreadController::class, 'show']);
Route::delete('threads/{channel}/{thread}', [App\Http\Controllers\ThreadController::class, 'destroy']);
Route::post('threads', [App\Http\Controllers\ThreadController::class, 'store']);
Route::get('threads/{channel}', [App\Http\Controllers\ThreadController::class, 'index']);
// Route::resource('threads', ThreadController::class);

Route::get('/threads/{channel}/{thread}/replies', [App\Http\Controllers\ReplyController::class, 'index']);
Route::post('/threads/{channel}/{thread}/replies', [App\Http\Controllers\ReplyController::class, 'store']);
Route::patch('replies/{reply}', [App\Http\Controllers\ReplyController::class, 'update']);
Route::delete('replies/{reply}', [App\Http\Controllers\ReplyController::class, 'destroy']);
Route::post('replies/{reply}/favorites', [App\Http\Controllers\FavoriteController::class, 'store']);
Route::delete('replies/{reply}/favorites', [App\Http\Controllers\FavoriteController::class, 'destroy']);
Route::get('/profiles/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profiles');
