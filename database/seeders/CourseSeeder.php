<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use Illuminate\Support\Str;
use App\Models\Material;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['title' => 'HSI Reguler', 'short_desc' => 'Kurikulum sistematis belajar aqidah Islam dari dasar hingga mahir.', 'type' => 'free', 'price' => null, 'color' => 'blue'],
            ['title' => 'Silsilah Ilmiah', 'short_desc' => "Kajian tematik mendalam berbagai cabang ilmu syar'i.", 'type' => 'free', 'price' => null, 'color' => 'emerald'],
            ['title' => 'Mahad Bahasa Arab', 'short_desc' => 'Kuasai Bahasa Arab dari nol hingga mahir dengan metode praktis.', 'type' => 'paid', 'price' => 150000, 'color' => 'orange'],
            ['title' => 'Tahsin Al-Quran', 'short_desc' => 'Perbaiki bacaan Al-Quran Anda secara privat dan bersanad.', 'type' => 'paid', 'price' => 100000, 'color' => 'purple'],
            ['title' => 'Fiqh Shalat', 'short_desc' => 'Pelajari fiqh shalat lengkap sesuai tuntunan.', 'type' => 'free', 'price' => null, 'color' => 'red'],
            ['title' => 'Sirah Nabawiyah', 'short_desc' => 'Menyelami perjalanan hidup Nabi shallallahu alaihi wasallam.', 'type' => 'free', 'price' => null, 'color' => 'gray'],
        ];

        foreach ($data as $d) {
            $course = Course::updateOrCreate(
                ['slug' => Str::slug($d['title'])],
                [
                    'title' => $d['title'],
                    'short_desc' => $d['short_desc'],
                    'type' => $d['type'],
                    'price' => $d['price'],
                    'currency' => 'IDR',
                    'color' => $d['color'],
                ]
            );

            // Add dummy materials if none exist
            if ($course->materials()->count() == 0) {
                $materials = [
                    ['title' => 'Pengantar & Mukadimah', 'duration' => '10 Menit', 'type' => 'video'],
                    ['title' => 'Definisi & Konsep Dasar', 'duration' => '15 Menit', 'type' => 'video'],
                    ['title' => 'Pembahasan Inti Bagian 1', 'duration' => '25 Menit', 'type' => 'video'],
                    ['title' => 'Pembahasan Inti Bagian 2', 'duration' => '20 Menit', 'type' => 'video'],
                    ['title' => 'Kesimpulan & Penutup', 'duration' => '10 Menit', 'type' => 'text'],
                ];

                foreach ($materials as $index => $m) {
                    Material::create([
                        'course_id' => $course->id,
                        'title' => $m['title'],
                        'duration' => $m['duration'],
                        'type' => $m['type'],
                        'order' => $index + 1,
                    ]);
                }
            }
        }
    }
}
