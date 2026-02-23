<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            ['name' => 'Kaos Dakwah Premium', 'price' => 85000, 'rating' => 4.8, 'sold_count' => 120, 'description' => 'Bahan Cotton Combed 30s, sablon plastisol awet.'],
            ['name' => 'Buku Panduan Sholat', 'price' => 45000, 'rating' => 4.9, 'sold_count' => 500, 'description' => 'Buku saku lengkap dengan ilustrasi.'],
            ['name' => 'Gamis Pria Modern', 'price' => 150000, 'rating' => 4.7, 'sold_count' => 85, 'description' => 'Bahan toyobo adem, cocok untuk sehari-hari.'],
            ['name' => 'Peci Rajut Yaman', 'price' => 25000, 'rating' => 4.6, 'sold_count' => 200, 'description' => 'Peci rajut tangan, nyaman dipakai.'],
            ['name' => 'Minyak Wangi Kasturi', 'price' => 35000, 'rating' => 4.8, 'sold_count' => 300, 'description' => 'Wangi tahan lama, non-alkohol.'],
            ['name' => 'Kurma Ajwa 1kg', 'price' => 250000, 'rating' => 5.0, 'sold_count' => 50, 'description' => 'Kurma nabi asli madinah.'],
            ['name' => 'Tasbih Digital LED', 'price' => 15000, 'rating' => 4.5, 'sold_count' => 1000, 'description' => 'Praktis untuk dzikir, ada lampu LED.'],
            ['name' => 'Hijab Segiempat Voal', 'price' => 55000, 'rating' => 4.9, 'sold_count' => 150, 'description' => 'Mudah dibentuk, tidak licin.'],
            ['name' => 'Sirwal Kantor', 'price' => 120000, 'rating' => 4.7, 'sold_count' => 90, 'description' => 'Celana cingkrang model formal.']
        ];

        foreach ($products as $p) {
            Product::create($p);
        }
    }
}
