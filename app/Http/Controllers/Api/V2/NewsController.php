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
                            'id' => $article->id,
                            'name' => $article->author->name ?? 'Unknown'
                        ],
                        'author' => $article->author->name ?? 'Unknown',
                        'title' => $article->title,
                        'description' => $article->description,
                        'url' => $article->url,
                        'urlToImage' => $article->url_to_image,
                        'publishedAt' => $article->published_at,
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

    public function NewsById($id)
    {
        $response = null;
        $statusCode = null;

        try {
            $article = Article::with('author', 'category')->findOrFail($id);

            $response = [
                'id' => $article->id,
                'title' => $article->title,
                'description' => $article->description,
                'content' => $article->content,
                'url' => $article->url,
                'url_to_image' => $article->url_to_image,
                'published_at' => $article->published_at,
                'author' => $article->author->name,
                'author_id' => $article->author->id,
                'category' => $article->category->name,
                'category_id' => $article->category->id,
            ];

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

    public function NewsAuthorCategory($id)
    {
        $response = null;
        $statusCode = null;
        try {

            // Obtener el artículo original
            $article = Article::with('author', 'category')->findOrFail($id);
            $author_id = $article->author_id;
            $category_id = $article->category_id;

            // Obtener los primeros 4 artículos por categoría, excluyendo el artículo original
            $categoryArticles = Article::with('author', 'category')
                ->where('category_id', $category_id)
                ->where('id', '!=', $id)  // Excluir el artículo original
                ->orderBy('published_at', 'desc')
                ->take(4)
                ->get()
                ->map(function ($article) {
                    return [
                        'source' => [
                            'id' => $article->id,
                            'name' => $article->author->name ?? 'Desconocido'
                        ],
                        'author' => $article->author->name ?? 'Desconocido',
                        'title' => $article->title,
                        'description' => $article->description,
                        'url' => $article->url,
                        'urlToImage' => $article->url_to_image,
                        'publishedAt' => $article->published_at,
                        'content' => $article->content,
                    ];
                });

            // Obtener los primeros 4 artículos por autor, excluyendo el artículo original
            $authorArticles = Article::with('author', 'category')
                ->where('author_id', $author_id)
                ->where('id', '!=', $id)  // Excluir el artículo original
                ->orderBy('published_at', 'desc')
                ->take(4)
                ->get()
                ->map(function ($article) {
                    return [
                        'source' => [
                            'id' => $article->id,
                            'name' => $article->author->name ?? 'Desconocido'
                        ],
                        'author' => $article->author->name ?? 'Desconocido',
                        'title' => $article->title,
                        'description' => $article->description,
                        'url' => $article->url,
                        'urlToImage' => $article->url_to_image,
                        'publishedAt' => $article->published_at,
                        'content' => $article->content,
                    ];
                });

            // Combina los resultados en una sola respuesta
            $response = [
                'categoryArticles' => $categoryArticles,
                'authorArticles' => $authorArticles,
            ];
            $statusCode = Response::HTTP_OK;
        } catch (\InvalidArgumentException $e) {
            Log::error($e->getMessage());
            $response = ['error' => 'Invalid parameters'];
            $statusCode = Response::HTTP_BAD_REQUEST;
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            $response = $th->getMessage();
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response()->json($response, $statusCode);
    }
}
