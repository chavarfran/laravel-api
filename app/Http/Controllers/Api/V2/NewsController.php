<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    public function getNews()
    {
        // Obtener la clave API desde el archivo .env
        $apiKey = env('NEWS_API_KEY');

        // URL de la API que deseas consultar
        $url = 'https://newsapi.org/v2/everything';

        // Parámetros de consulta
        $response = Http::get($url, [
            'q' => 'Odatv.com',
            'from' => '2024-09-06',
            'sortBy' => 'publishedAt',
            'apiKey' => $apiKey,
        ]);

        // Retornar la respuesta de la API
        return $response->json();
    }
}
