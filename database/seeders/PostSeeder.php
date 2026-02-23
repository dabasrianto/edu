<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories first
        $categories = [
            ['name' => 'Pengumuman', 'color' => 'blue'],
            ['name' => 'Artikel Islam', 'color' => 'green'],
            ['name' => 'Berita HSI', 'color' => 'orange'],
            ['name' => 'Tips Belajar', 'color' => 'purple']
        ];

        $categoryIds = [];
        foreach ($categories as $cat) {
            $c = \App\Models\Category::firstOrCreate(
                ['name' => $cat['name']],
                ['color' => $cat['color']]
            );
            $categoryIds[] = $c->id;
        }
        
        foreach (range(1, 15) as $index) {
            Post::create([
                'title' => 'Judul Artikel Menarik Ke-' . $index,
                'slug' => 'artikel-' . $index . '-' . uniqid(),
                'type' => 'article',
                'content' => "Ini adalah isi konten artikel dummy nomor {$index}. " . str_repeat("Lorem ipsum dolor sit amet. ", 5),
                'image' => null, 
                // Use valid category IDs
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'status' => 'published',
                'order' => $index,
                'created_at' => now()->subDays(rand(0, 60)),
            ]);
        }
    }
}
