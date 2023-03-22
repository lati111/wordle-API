<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ClientController extends Controller
{
    public function newClient(Request $request) {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|min:4|max:28|unique:client,name',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $secretKey = Str::random(64);

        $client = new Client();
        $client->name = $request->name;
        $client->secretKey = $secretKey;
        $client->save();

        $clientKey = $client->uuid;

        return response()->json([
            'status' => true,
            'message' => 'Client '.$request->name.' created',
            'access_key' => $clientKey,
            'secret_key' => $secretKey,
        ], 200);
    }
}
