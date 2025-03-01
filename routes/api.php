<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::get('/results', [AuthController::class, 'results'])->middleware('authapi');
Route::post('/logout', function (Request $request) {
    $request->user()->token()->revoke();
    return response()->json(['message' => 'Successfully logged out']);
})->middleware('authapi');
