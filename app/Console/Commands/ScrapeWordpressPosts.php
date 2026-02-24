<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Category;
use App\Models\AppSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ScrapeWordpressPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrape-wp {url?} {--category_id=} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape posts from a WordPress site using REST API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = AppSetting::firstOrCreate(['key' => 'main_settings']);
        $blogConfig = $settings->blog_config ?? [];

        $url = $this->argument('url') ?? (($blogConfig['wp_sync_enabled'] ?? false) ? ($blogConfig['wp_sync_url'] ?? null) : null);
        
        if (!$url) {
            $this->error("No URL provided and WP Sync is disabled or has no URL.");
            return 1;
        }

        $categoryId = $this->option('category_id') ?? ($blogConfig['wp_sync_category_id'] ?? null);
        $limit = $this->option('limit') ?? ($blogConfig['wp_sync_limit'] ?? 10);

        $this->info("Fetching posts from: {$url} (Limit: {$limit})...");

        try {
            // WordPress REST API endpoint
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) HSI-Edu/1.0'])
                ->withoutVerifying()
                ->get(rtrim($url, '/') . '/wp-json/wp/v2/posts', [
                    'per_page' => $limit,
                    '_embed' => 1, 
                ]);

            if (!$response->successful()) {
                $this->error("Failed to fetch posts. Status: " . $response->status());
                return 1;
            }

            $wpPosts = $response->json();
            $count = 0;

            foreach ($wpPosts as $wpPost) {
                $title = html_entity_decode($wpPost['title']['rendered']);
                $slug = $wpPost['slug'];
                
                // Check if post already exists
                $existingPost = Post::where('slug', $slug)->first();
                
                $content = $wpPost['content']['rendered'];
                $imageUrl = null;

                // Extract featured image
                if (isset($wpPost['_embedded']['wp:featuredmedia'][0]['source_url'])) {
                    $imageUrl = $wpPost['_embedded']['wp:featuredmedia'][0]['source_url'];
                }

                $imagePath = $existingPost ? $existingPost->image : null;
                
                if ($imagePath === null && $imageUrl) {
                    try {
                        $imageContent = Http::withoutVerifying()
                            ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                            ->get($imageUrl)->body();
                        $filename = 'posts/' . Str::random(10) . '_' . basename(parse_url($imageUrl, PHP_URL_PATH));
                        Storage::disk('public')->put($filename, $imageContent);
                        $imagePath = $filename;
                    } catch (\Exception $e) {
                        $this->warn("Could not download image for: {$title}");
                    }
                }

                $postData = [
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                    'image' => $imagePath,
                    'category_id' => $categoryId ?? $this->getDefaultCategoryId(),
                    'status' => 'published',
                    'type' => 'article',
                ];

                if ($existingPost) {
                    $existingPost->update($postData);
                    $this->line("Updated: {$title}");
                } else {
                    Post::create($postData);
                    $this->info("Imported: {$title}");
                }
                
                $count++;
            }

            $this->info("Synchronization finished. Processed {$count} items.");

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            \Log::error("WP Sync Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function getDefaultCategoryId()
    {
        return Category::firstOrCreate(['name' => 'WordPress'], ['color' => 'blue'])->id;
    }
}
