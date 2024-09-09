<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

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

    public function News(Request $request)
    {
        $response = null;
        $statusCode = null;

        try {
            $articles = Article::has('category')
                ->where('category_id', $request->category_id)
                ->orderBy('published_at', 'desc')
                ->get();

            $response = $articles;
            $statusCode = Response::HTTP_OK;
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $response = 'Error critico del servidor';
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response()->json(
            $response,
            $statusCode
        );
    }
}
