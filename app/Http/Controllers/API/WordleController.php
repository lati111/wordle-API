<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wordle\Session;
use App\Models\Wordle\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WordleController extends Controller
{
    public function newGame(Request $request) {
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
            ], 401);
        }

        $wordCount = 10;

        $session = new Session();
        $session->status = "in progress";
        $session->user = Auth::user()->uuid;
        if ($request->length !== null) {
            $session->words = $request->length;
            $wordCount = $request->length;
        }
        $session->save();
        $sessionUuid = $session->uuid;

        $words = $this->getWords($wordCount);
        for ($i=0; $i < count($words); $i++) {
            $word = new Word();
            $word->word = $words[$i];
            $word->index = $i + 1;
            $word->session = $sessionUuid;
            $word->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Wordle session started',
            'uuid' => $sessionUuid,
            'words' => $words,
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
