<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\AuthController;
use App\Models\User;
use App\Models\Note;
use App\Models\Role;


Route::get('/', function () {
    return response()->json(['message' => 'Welcome to Notes POC Application']);
});
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

// Route::get('/', function () {
//     return view('welcome');
// });



// Route::post('/login' , [AuthController::class, 'login']);

// Route::post('/register', [AuthController::class, 'register']);




// Route::post('/users/{userId}/assign-role', [RoleController::class, 'assignRole']);


// Route::middleware('auth')->group(function () {
//     Route::get('/notes', [NoteController::class, 'index']);
//     Route::post('/notes/create', [NoteController::class, 'store']);
// });



Route::get('/setup-db', function () {
    // Create tables if they don't exist
    Role::createTable();
    User::createTable();
    Note::createTable();

    return "Database tables created successfully!";
});