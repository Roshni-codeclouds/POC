<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route::middleware(['auth.api' , 'role:Admin'])->group(function(){
//     Route::post('/notes/create-for-user/{user_id}', [NoteController::class, 'createForUser']); 
// });

// Route::middleware(['auth:api', 'role:User'])->group(function () {
//     Route::post('/notes/create', [NoteController::class, 'createForSelf']); // Users can create their own notes
// });



// FOR LOGIN ADN REGIS
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']); // Register
    Route::post('/login', [AuthController::class, 'login']);       // Login
});


Route::middleware(['auth:api'])->group(function () {
    Route::get('/notes/{id?}/{date?}', [NoteController::class, 'index']); // List notes
    Route::get('/notes/{id}', [NoteController::class, 'show']); // View specific note
    Route::post('/notes/create', [NoteController::class, 'createForSelf']); // Users create their own note

    Route::delete('/notes/{id}', [NoteController::class, 'destroy']); // Delete a note
});


Route::middleware(['auth:api', 'role:Admin'])->group(function () {
    Route::post('/notes/create-for-user', [NoteController::class, 'createForUser']); // Admin creates note for any user
});
