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
    return view('auth.login');
});



Auth::routes(['verify' => true]);

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/home', [App\Http\Controllers\ThreadController::class, 'index'])->name('threads');

Route::get('threads/create', [App\Http\Controllers\ThreadController::class, 'create']);
Route::get('threads/search', [App\Http\Controllers\SearchController::class, 'show']);
Route::get('threads/{channel}/{thread}', [App\Http\Controllers\ThreadController::class, 'show']);
Route::patch('threads/{channel}/{thread}', [App\Http\Controllers\ThreadController::class, 'update'])->name('thread.update');

Route::delete('threads/{channel}/{thread}', [App\Http\Controllers\ThreadController::class, 'destroy']);
Route::post('threads', [App\Http\Controllers\ThreadController::class, 'store'])->middleware(['verified', 'auth']);
Route::get('threads/{channel}', [App\Http\Controllers\ThreadController::class, 'index']);
Route::post('locked-threads/{thread}', [App\Http\Controllers\ThreadLockedController::class, 'store'])->name('locked-threads.store')->middleware('admin');

Route::delete('locked-threads/{thread}', [App\Http\Controllers\ThreadLockedController::class, 'destroy'])->name('locked-threads.destroy')->middleware('admin');
// Route::resource('threads', ThreadController::class);

Route::get('/threads/{channel}/{thread}/replies', [App\Http\Controllers\ReplyController::class, 'index']);
Route::post('/threads/{channel}/{thread}/replies', [App\Http\Controllers\ReplyController::class, 'store']);
Route::patch('replies/{reply}', [App\Http\Controllers\ReplyController::class, 'update']);
Route::delete('replies/{reply}', [App\Http\Controllers\ReplyController::class, 'destroy'])->name('replies.destroy');

Route::post('/replies/{reply}/best', [App\Http\Controllers\BestRepliesController::class, 'store'])->name('best-replies.store');

Route::post('/threads/{channel}/{thread}/subscriptions', [App\Http\Controllers\ThreadSubscriptionController::class, 'store'])->middleware('auth');
Route::delete('/threads/{channel}/{thread}/subscriptions', [App\Http\Controllers\ThreadSubscriptionController::class, 'destroy'])->middleware('auth');

Route::post('replies/{reply}/favorites', [App\Http\Controllers\FavoriteController::class, 'store']);
Route::delete('replies/{reply}/favorites', [App\Http\Controllers\FavoriteController::class, 'destroy']);
Route::get('/profiles/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profiles');

Route::get('/profiles/{user}/notifications', [App\Http\Controllers\UserNotificationController::class, 'index']);
Route::delete('/profiles/{user}/notifications/{notification}', [App\Http\Controllers\UserNotificationController::class, 'destroy']);

Route::get('/api/users', [App\Http\Controllers\Api\UsersControllers::class, 'index']);
Route::post('/api/users/{user}/avatar', [App\Http\Controllers\Api\UserAvatarController::class, 'store'])->middleware('auth')->name('avatar');
