<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
                ->get()
                ->map(function ($article) {
                    return [
                        'source' => [
                            'id' => null,  // You can adjust this if you have a source id
                            'name' => $article->author->name ?? 'Unknown'  // Assuming author name is the source
                        ],
                        'author' => $article->author->name ?? 'Unknown',  // Handling null author case
                        'title' => $article->title,
                        'description' => $article->description,
                        'url' => $article->url,
                        'urlToImage' => $article->url_to_image,
                        'publishedAt' => $article->published_at,  // Format the date correctly
                        'content' => $article->content,
                    ];
                });

            $response = $articles;
            $statusCode = Response::HTTP_OK;
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $response = $th->getMessage();
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response()->json(
            $response,
            $statusCode
        );
    }
}
