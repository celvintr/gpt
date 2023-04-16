<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatGPTController extends Controller
{
    public function index()
    {
        return view('chat');
    }

    public function chat(Request $request)
    {
        $input = $request->input('text');
        $history = $request->session()->get('history', '');

        // Limitar el historial a las últimas 2 interacciones
        $recent_history = implode("\n", array_slice(explode("\n", $history), -5)) . "\n";

        $client = new Client();
        $response = $client->post('https://api.openai.com/v1/engines/davinci/completions', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            ],
            'json' => [
                'prompt' => $recent_history . "Usuario: " . $input . "\nAsistente:",
                'max_tokens' => 300,
                'n' => 1,
                'stop' => ["\n"],
                'temperature' => 0.9, // Cambiar la temperatura a 0.8
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        $reply = trim($data['choices'][0]['text']);

        // Actualizar el historial de la conversación y guardarlo en la sesión.
        $history .= "Usuario: " . $input . "\nAsistente: " . $reply . "\n";
        $request->session()->put('history', $history);

        return response()->json(['response' => $reply]);

    }
}
