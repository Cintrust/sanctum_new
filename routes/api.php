<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

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

Route::post("login",function (Request $request){
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',

    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return ["token"=>$user->createToken($request->email)->plainTextToken];

})->name("sanctum_login");
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
})->name("me");
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    // Revoke all tokens...
    $request->user()
        ->tokens()->delete();

    return response(null,204);
})->name("sanctum_logout");

