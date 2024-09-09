<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    public function NewsEverything(Request $request)
    {
        // Obtener la clave API desde el archivo .env
        $apiKey = env('NEWS_API_KEY');

        // URL de la API que deseas consultar
        $url = 'https://newsapi.org/v2/everything';

        // Parámetros de consulta
        $response = Http::get($url, [
            'q' => $request->q,
            'from' => $request->from,
            'sortBy' => $request->sortBy,
            'languaje' => 'en',
            'apiKey' => $apiKey,
        ]);

        // Retornar la respuesta de la API
        return $response->json();
    }

    public function NewsByCategory(Request $request)
    {
        // Obtener la clave API desde el archivo .env
        $apiKey = env('NEWS_API_KEY');

        // URL de la API que deseas consultar
        $url = 'https://newsapi.org/v2/top-headlines';

        // Parámetros de consulta
        $response = Http::get($url, [
            'q' => $request->q,
            'category' => $request->category,
            'languaje' => $request->language,
            'apiKey' => $apiKey,
        ]);

        // Retornar la respuesta de la API
        return $response->json();
    }
}
