<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register')->middleware(['auth:sanctum', 'ability:admin,superAdmin']);
    Route::post('logout', 'logout')->middleware('auth:sanctum');
    Route::get('users','index')->middleware(['auth:sanctum', 'ability:admin,superAdmin']);
});

Route::controller(BookController::class)->group(function(){
    Route::post('book','store')->middleware(['auth:sanctum', 'ability:user,superAdmin']);
    Route::put('book/{id}','update')->middleware(['auth:sanctum', 'ability:user,superAdmin']);
    Route::delete('book/{id}','destroy')->middleware(['auth:sanctum', 'ability:user,superAdmin']);
    Route::get('book/{id}','show')->middleware(['auth:sanctum', 'ability:user,superAdmin']);
    Route::get('books','index');
    Route::post('bulkBooks','bulk')->middleware(['auth:sanctum', 'ability:superAdmin']);
});

Route::controller(AuthorController::class)->group(function(){
    Route::post('author','store')->middleware(['auth:sanctum', 'ability:user,superAdmin']);
    Route::get('authors','index');
});

Route::controller(CategoryController::class)->group(function(){
    Route::post('category','store')->middleware(['auth:sanctum', 'ability:user,superAdmin']);
    Route::get('categories','index');
});


Route::get("/hello", function (Request $request) {
    return response()->json("Hello");
});
