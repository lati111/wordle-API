<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Wordle\Session;
use App\Models\Wordle\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Log;

class WordleController extends Controller
{
    public function newGame(Request $request, string $client_key) {
        if (Client::where("uuid", "=", $client_key)->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'client error',
                'errors' => "No client under key '".$client_key."' exists",
            ], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'length' => 'nullable|integer|min:1|max:25',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $wordCount = 10;

        $session = new Session();
        $session->status = "in progress";
        $session->user = Auth::user()->uuid;
        if ($request->length !== null) {
            $session->words = $request->length;
            $wordCount = $request->length;
        }
        $session->client = $client_key;
        $session->save();
        $sessionUuid = $session->uuid;
        $words = $this->getWords($wordCount);

        return response()->json([
            'status' => true,
            'message' => 'Wordle session started',
            'uuid' => $sessionUuid,
            'words' => $words,
        ], 200);
    }

    public function setScore(Request $request, string $client_key, string $session_uuid) {
        //| client validation
        if (Client::where("uuid", "=", $client_key)->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'client error',
                'errors' => "No client under key '".$client_key."' exists",
            ], 404);
        }

        //| session validation
        $session =
            Session::where("uuid", "=", $session_uuid)
            ->where("user", "=", Auth::user()->uuid)
            ->where("client", "=", $client_key);

        if ($session->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => "No session under key '".$session_uuid."' exists",
            ], 404);
        }

        //| score validation
        $validator = Validator::make(
            $request->all(),
            [
                'score' => 'required|integer|max:250',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        //| data submitting
        $session = $session->first();
        $round = $session->round + 1;
        if ($session->round > $session->words || $session->status === "finished") {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => 'This session had already been concluded'
            ], 400);
        }

        $session->score = $session->score + $request->score;
        $session->round = $round;
        if ($session->round > $session->words) {
            $session->status = "finished";
            $session->save();
            return response()->json([
                'status' => true,
                'message' => 'Score has been set and the session has concluded',
            ], 200);
        } else {
            $session->save();
            return response()->json([
                'status' => true,
                'message' => 'Score has been set.',
                'current_round' => $round,
            ], 200);
        }
    }

    public function topScore(Request $request, string $client_key) {
        //| validate client
        if (Client::where("uuid", "=", $client_key)->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'client error',
                'errors' => "No client under key '".$client_key."' exists",
            ], 404);
        }

        //| validate data
        $validator = Validator::make(
            $request->all(),
            [
                'offset' => 'nullable|integer',
                'limit' => 'nullable|integer|max:100',
                'date' => 'nullable|date',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        //| build score query
        $scores =
            Session::where("client", "=", $client_key)
            ->where("status", "=", "finished")
            ->offset($request->input("offset", 0));
        if ($request->date !== null) {
            $scores = $scores->whereDate('updated_at','=', $request->date);
        }
        $scores = $scores
            ->orderBy("score", "desc")
            ->take($request->input("limit", 10))->get();

        //| response
        $scoreArray = [];
        foreach($scores as $score) {
            $scoreArray[] = [
                "score" => $score->score,
                "user" => $score->Owner->name,
                "timestamp" => $score->updated_at,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'List of top scores',
            'scores' => $scoreArray,
        ], 200);
    }

    public function getScore(string $client_key, string $session_uuid) {
        //| client validation
        if (Client::where("uuid", "=", $client_key)->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'client error',
                'errors' => "No client under key '".$client_key."' exists",
            ], 404);
        }

        //| session validation
        $session =
            Session::where("uuid", "=", $session_uuid)
            ->where("user", "=", Auth::user()->uuid)
            ->where("client", "=", $client_key);

        if ($session->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => "No session under key '".$session_uuid."' exists",
            ], 404);
        }

        //| response
        return response()->json([
            'status' => true,
            'message' => 'Current score',
            'session_uuid' => $session_uuid,
            'session_status' => $session->status,
            'score' => $session->first()->score,
        ], 200);
    }

    public function getRound(string $client_key, string $session_uuid) {
        //| client validation
        if (Client::where("uuid", "=", $client_key)->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'client error',
                'errors' => "No client under key '".$client_key."' exists",
            ], 404);
        }

        //| session validation
        $session =
            Session::where("uuid", "=", $session_uuid)
            ->where("user", "=", Auth::user()->uuid)
            ->where("client", "=", $client_key);

        if ($session->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => "No session under key '".$session_uuid."' exists",
            ], 404);
        }

        //| response
        $session = $session->first();
        return response()->json([
            'status' => true,
            'message' => 'Current round',
            'session_uuid' => $session_uuid,
            'session_status' => $session->status,
            'round' => $session->round,
        ], 200);
    }

    private function getWords(int $amount) {
        $endpoint = "https://random-word-api.herokuapp.com/word";
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $endpoint, ['query' => [
            'length' => 5,
            'number' => $amount,
        ]]);

        $statusCode = $response->getStatusCode();
        switch($statusCode) {
            case 200:
                return json_decode($response->getBody());
            case 404:
                throw new HttpException(404, "Could not reach word generation API");
            default:
                throw new HttpException(424, "An internal error has occured");

        }

    }
}
