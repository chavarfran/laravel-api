<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Author;
use App\Models\Category;
use App\Models\Article;
use Illuminate\Support\Facades\Http;
class FetchArticlesCommand extends Command
{
    // El nombre y la descripción del comando

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from the API and store them in the database';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching articles from the API...');

        // Categorías a iterar
        $categories = ['business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology'];

        // Clave API
        $apiKey = env('NEWS_API_KEY');
        $url = 'https://newsapi.org/v2/top-headlines';

        // Recorrer cada categoría
        foreach ($categories as $category) {
            $this->info("Fetching articles for category: {$category}");

            // Llamada a la API para cada categoría
            $response = Http::get($url, [
                'category' => $category,
                'apiKey' => $apiKey,
                'language' => 'en',
            ]);

            $articles = $response->json()['articles'] ?? [];

            foreach ($articles as $data) {
                // Buscar o crear el autor
                $author = Author::firstOrCreate([
                    'name' => $data['author'] ?? 'Unknown Author',
                ]);

                // Buscar o crear la categoría
                $categoryRecord = Category::firstOrCreate([
                    'name' => $category,
                ]);

                // Crear el artículo
                Article::create([
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'content' => $data['content'],
                    'url' => $data['url'],
                    'url_to_image' => $data['urlToImage'] ?? null,
                    'published_at' => $data['publishedAt'],
                    'author_id' => $author->id,
                    'category_id' => $categoryRecord->id,
                ]);
            }
        }

        $this->info('Articles fetched and stored successfully.');
    }
}
