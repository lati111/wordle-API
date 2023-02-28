<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
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
    public function createUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'username' => 'required',
                    'email' => 'required|email|unique:users,email',
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

            $user = User::create([
                'name' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            event(new Registered($user));

            return response()->json([
                'status' => true,
                'message' => "User Created Successfully. A confirmation email has been sent to their account."
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function loginUser(Request $request)
    {
        try {
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

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function passwordForgot(Request $request) //sends a password reset email
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

            $user = User::where('email', "=", $request->only('email'));
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

    public function resetPassword(Request $request) //resets a password
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

            $user = User::where('email', "=", $request->email);
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

    public function failure_wrong_method()
    {
        return response()->json([
            'status' => false,
            'message' => 'Registering is only allowed through a get request, with a body containing a name, email and password in the form data',
        ], 401);
    }
}
