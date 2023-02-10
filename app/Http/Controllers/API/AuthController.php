<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Email_Verify;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function createUser(Request $request)
    {
        try {
            //Validated
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

            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
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

    public function getVerifyToken(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email|exists:users,email',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No account matching this email exists',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $uuid = User::where('email', $request->input("email"))->first()->uuid;
            $verification = Email_Verify::where('user_uuid', '=', $uuid)->first();
            if ($verification !== null) {
                $verification->delete();
            }

            $verification = new Email_Verify();
            $verification->user_uuid = $uuid;
            $verification->save();

            return response()->json([
                'status' => true,
                'message' => 'Email verification started',
                'token' => $uuid
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => "an error has occurred: ".$th->getMessage()
            ], 500);
        }


    }

    public function verify(string $token) {

    }

    public function failure_no_token() {
        return response()->json([
            'status' => false,
            'message' => 'No token found in Authorization header',
        ], 401);
    }

    public function failure_wrong_method() {
        return response()->json([
            'status' => false,
            'message' => 'Registering is only allowed through a get request, with a body containing a name, email and password in the form data',
        ], 401);
    }
}
