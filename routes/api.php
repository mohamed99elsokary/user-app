<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('update', [AuthController::class, 'update']);
Route::get('mail', function () {
    $data = array('name' => "Virat Gandhi");
    return Mail::send([], $data, function ($message) {
        $message->to('mohamed99elsokary@gmail.com', 'Tutorials Point')->subject('Laravel Basic Testing Mail');
        $message->from('joker171joker72@gmail.com', 'Tutorials Point');
    });
});
