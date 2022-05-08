<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateRequest;
use App\Http\Resources\testResource;
use App\Http\Resources\tokenResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($request['password']);
        $email = $validated['email'];
        #function to send the email goes here
        $user = User::create($validated);
        $token = $user->createToken('LaravelAuthApp')->accessToken;
        return $this->dataResponse(['user' => $user, 'token' => $token]);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $user = new testResource($user);
                $token = new tokenResource($token);
                return $this->dataResponse(['user' => $user, 'token' => $token]);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => 'User does not exist'];
            return response($response, 422);
        }
    }
    public function update(UpdateRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                if ($request->name != null && $request->name != "") {
                    $user->name = $request->name;
                }
                $user->save();
                $response = ["message" => 'User Updated Successfully'];
                return response($response, 200);
            }
        } else {
            $response = ["message" => "Password mismatch"];
            return response($response, 422);
        }
    }
}
