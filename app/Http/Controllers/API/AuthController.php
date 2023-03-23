<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Models\Client;
use App\Models\Token;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function createUser(Request $request, string $client_key)
    {
        try {
            if (Client::where("uuid", "=", $client_key)->count() === 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'client error',
                    'errors' => "No client under key '".$client_key."' exists",
                ], 404);
            }

            $validateUser = Validator::make(
                $request->all(),
                [

                    'username' => 'required',
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (User::where("email", "=", $request->email)->where("client", "=", $client_key)->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => 'Email is already taken'
                ], 400);
            }

            $user = new User();
            $user->name = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->client = $client_key;
            $user->save();

            // event(new Registered($user));

            return response()->json([
                'status' => true,
                'message' => "User Created Successfully. Please log in to create a token"
            ], 200);

            // return response()->json([
            //     'status' => true,
            //     'message' => "User Created Successfully. A confirmation email has been sent to their account."
            // ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function loginUser(Request $request, string $client_key)
    {
        try {
            if (Client::where("uuid", "=", $client_key)->count() === 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'client error',
                    'errors' => "No client under key '".$client_key."' exists",
                ], 404);
            }

            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }


            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password, 'client' => $client_key])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->where("client", "=", $client_key)->first();
            $accessToken = $user->createToken("auth", ["auth"]);
            $refreshToken = $user->createToken("refresh", ["refresh"]);
            Token::where("id", "=", $accessToken->accessToken->id)->first()->update(["expires_at" => now()->addDays(1)]);
            Token::where("id", "=", $refreshToken->accessToken->id)->first()->update(["expires_at" => now()->addDays(7)]);

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'accessToken' => $accessToken->plainTextToken,
                'refreshToken' => $refreshToken->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function refreshToken(Request $request) //consumes and returns new access token
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'accessToken' => 'required'
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }


        $accessToken = Token::findToken($request->accessToken);
        if ($accessToken !== null) {
            $accessToken = Token::where([["token", "=", $accessToken->token], ["name", "=", "auth"]]);
            if ($accessToken->count() === 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Access token has already been consumed.',
                ], 401);
            }
            $accessToken = $accessToken->first();

            if ($accessToken->Owner->id === Auth::user()->id) {
                $accessToken->delete();
                $user = $accessToken->Owner;

                $accessToken = $user->createToken("auth", ["auth"]);
                Token::where("id", "=", $accessToken->accessToken->id)->first()->update(["expires_at" => now()->addHours(1)]);

                return response()->json([
                    'status' => true,
                    'message' => 'Access token refreshed',
                    'accessToken' => $accessToken->plainTextToken
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tokens do not belong to the same set.',
                ], 401);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Access token has already been consumed.',
            ], 401);
        }
    }

    public function passwordForgot(Request $request, string $client_key) //sends a password reset email
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::where('email', "=", $request->only('email'))->where('client', "=", $client_key);
            if ($user->count() > 0) {
                $token = Password::createToken($user->first());
                Mail::to($request->only('email'))->send(new ForgotPassword("https://www.google.com", $token));
            }

            return response()->json([
                'status' => true,
                'message' => 'If the user exists, a password reset email has been sent.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request, string $client_key) //resets a password
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'token' => 'required',
                    'email' => 'required|email|exists:users,email',
                    'password' => 'required|min:4'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::where('email', "=", $request->email)->where('client', "=", $client_key);
            if (Password::tokenExists($user->first(), $request->token)) {
                $user->update(["password" => Hash::make($request->password)]);

                event(new PasswordReset($user));

                return response()->json([
                    'status' => true,
                    'message' => 'Password reset has been successful.',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'token is invalid, please try again with a valid token.',
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }


    }


    public function verify(Request $request) //verifies an email address
    {
        $user = User::find($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException();
        }

        if ($user->markEmailAsVerified())
            event(new Verified($user));

        // return redirect($this->redirectPath())->with('verified', true);
    }

    public function failure_no_token()
    {
        return response()->json([
            'status' => false,
            'message' => 'No token found in Authorization header',
        ], 401);
    }

    public function failure_false_token()
    {
        return response()->json([
            'status' => false,
            'message' => 'User token is expired or invalid',
        ], 401);
    }

    public function failure_wrong_method()
    {
        return response()->json([
            'status' => false,
            'message' => 'Registering is only allowed through a post request, with a body containing an email and password in the form data',
        ], 401);
    }
}
