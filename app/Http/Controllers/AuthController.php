<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivateRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordConfirmRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateRequest;
use App\Http\Resources\testResource;
use App\Http\Resources\tokenResource;
use App\Mail\CloudHostingProduct as MailCloudHostingProduct;
use App\Models\User;
use App\Models\user_logins;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stevebauman\Location\Facades\Location;

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

            $response = ["message" => "email not sent"];
            return response($response, 422);
        }
        return $this->dataResponse(['user' => $user, 'token' => $token]);
    }

    public function login(LoginRequest $request)
    {
        $clientIP = request()->ip();

        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $is_activated = $user["is_activated"];
                if ($is_activated == true) {
                    $position = Location::get("102.46.119.77");
                    // dd($position);
                    $user_logins = user_logins::create([
                        'ip' => $position->ip,
                        'countryName' => $position->countryName,
                        'countryCode' => $position->countryCode,
                        'cityName' => $position->cityName,
                        'user_id' => $user->id
                    ]);
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
    public function reset_password(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {

            $random = Str::random(40);
            $user->reset_password_code = $random;
            $user->save();
            try {

                $email = "mohamed99elsokary@gmail.com";
                Mail::to($email)->send(new MailCloudHostingProduct($random));
            } catch (\Throwable $th) {

                $response = ["message" => "email not sent"];
                return response($response, 422);
            }
            return $this->dataResponse(['user' => $user]);
        }
    }
    public function reset_password_confirm(ResetPasswordConfirmRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user and $user->reset_password_code == $request->reset_code) {
            $user->password = bcrypt($request->password);
            $user->reset_password_code = '';
            $user->save();

            return $this->dataResponse(['user' => $user]);
        } else {

            return $this->dataResponse(["message" => "email or reset password code is invalid"]);
        }
    }
}
