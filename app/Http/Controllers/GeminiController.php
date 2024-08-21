<?php

namespace App\Http\Controllers;

use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Http\Request;

class GeminiController extends Controller
{
    public function index()
    {
        return view('gemini.index');
    }

    public function generate(Request $request)
    {
        $prompt = $request->input('prompt');
        $generatedText = Gemini::generateText($prompt);

        return response()->json(['generatedText' => $generatedText]);
    }
}
