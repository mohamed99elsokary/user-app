<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivateRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateRequest;
use App\Http\Resources\testResource;
use App\Http\Resources\tokenResource;
use App\Mail\CloudHostingProduct as MailCloudHostingProduct;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($request['password']);
        // $email = $validated['email'];
        $email = "mohamed99elsokary@gmail.com";
        $random = Str::random(40);

        $validated['activation_code'] = $random;
        $user = User::create($validated);
        $token = $user->createToken('LaravelAuthApp')->accessToken;

        #function to send the email goes here
        try {

            Mail::to($email)->send(new MailCloudHostingProduct($random));
        } catch (\Throwable $th) {
            dd($th);
            $response = ["message" => "email not sent"];
            return response($response, 422);
        }
        return $this->dataResponse(['user' => $user, 'token' => $token]);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $is_activated = $user["is_activated"];
                if ($is_activated == true) {
                    $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                    $user = new testResource($user);
                    $token = new tokenResource($token);
                    return $this->dataResponse(['user' => $user, 'token' => $token]);
                } else {
                    $response = ["message" => "please activate your account first"];
                    return response($response, 422);
                }
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
    public function activate(ActivateRequest $request)
    {
        $user = User::where('id', $request->id)->first();
        if ($user->activation_code == $request->activation_code) {
            $user->is_activated = true;
            $user->save();
            $response = ["message" => 'User activated Successfully'];
            return response($response, 200);
        } else {
            $response = ["message" => 'activaten code is incorrect'];
            return response($response, 400);
        }
    }
}
