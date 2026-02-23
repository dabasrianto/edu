<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\Post;
use App\Models\Category;
use App\Models\Quiz;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banner;
use Illuminate\Support\Str;
use App\Models\BankAccount;
use App\Models\QuizQuestion;
use App\Models\QuizOption;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin Account
        User::updateOrCreate(
            ['email' => 'mas@abd.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'role' => 'admin',
                'is_admin' => true,
                'is_active' => true,
                'balance' => 0,
            ]
        );

        $this->call(CourseSeeder::class);

        // Categories (6 items)
        $categories = [
            ['name' => 'Umum', 'color' => 'blue'],
            ['name' => 'Pengumuman', 'color' => 'emerald'],
            ['name' => 'Kajian', 'color' => 'purple'],
            ['name' => 'Event', 'color' => 'orange'],
            ['name' => 'Sejarah', 'color' => 'red'],
            ['name' => 'Bahasa Arab', 'color' => 'teal'],
        ];
        foreach ($categories as $c) {
            Category::updateOrCreate(
                ['slug' => Str::slug($c['name'])], // Key to find
                [
                    'name' => $c['name'],
                    'color' => $c['color']
                ]
            );
        }

        // Blogs (6 items)
        $blogTitles = [
            'Belajar Tauhid Dasar',
            'Panduan Wudhu Praktis',
            'Jadwal Kajian Pekan Ini',
            'Ringkasan Materi HSI',
            'Tips Konsisten Belajar',
            'Informasi Kegiatan Akhir Pekan',
        ];
        foreach ($blogTitles as $i => $title) {
            $catId = Category::inRandomOrder()->value('id');
            Post::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'type' => 'blog',
                    'content' => 'Konten dummy untuk ' . $title . '. Lorem ipsum dolor sit amet.',
                    'status' => 'published',
                    'order' => $i + 1,
                    'category_id' => $catId,
                ]
            );
        }

        // Products (6 items)
        $productNames = [
            'Buku Aqidah',
            'Notebook Kajian',
            'Pena Arab',
            'Tas Belajar',
            'Poster Ilmiah',
            'Stiker Motivasi',
        ];
        foreach ($productNames as $name) {
            Product::updateOrCreate(
                ['name' => $name],
                [
                    'description' => 'Produk dummy ' . $name,
                    'price' => rand(25000, 150000),
                    'rating' => rand(40, 50) / 10,
                    'sold_count' => rand(10, 200),
                    'link' => '#',
                    'is_active' => true,
                ]
            );
        }

        // Quizzes (6 items)
        $quizData = [
            ['title' => 'Kuis Tauhid 1', 'category' => 'Aqidah', 'color' => 'blue', 'type' => 'wajib', 'duration_minutes' => 15],
            ['title' => 'Kuis Fiqh 1', 'category' => 'Fiqh', 'color' => 'green', 'type' => 'sunnah', 'duration_minutes' => 10],
            ['title' => 'Kuis Hadits 1', 'category' => 'Hadits', 'color' => 'yellow', 'type' => 'wajib', 'duration_minutes' => 20],
            ['title' => 'Kuis Sirah 1', 'category' => 'Sirah', 'color' => 'purple', 'type' => 'sunnah', 'duration_minutes' => 12],
            ['title' => 'Kuis Adab 1', 'category' => 'Adab', 'color' => 'red', 'type' => 'wajib', 'duration_minutes' => 8],
            ['title' => 'Kuis Bahasa Arab 1', 'category' => 'Bahasa', 'color' => 'orange', 'type' => 'sunnah', 'duration_minutes' => 18],
        ];
        foreach ($quizData as $q) {
            Quiz::updateOrCreate(
                ['title' => $q['title']],
                [
                    'description' => 'Kuis dummy ' . $q['category'],
                    'category' => $q['category'],
                    'color' => $q['color'],
                    'type' => $q['type'],
                    'duration_minutes' => $q['duration_minutes'],
                    'is_active' => true,
                    'show_result' => true,
                ]
            );
        }

        // Banners (6 items)
        $bannerTitles = [
            'Promo Kursus Baru',
            'Jadwal Kajian Spesial',
            'Update Fitur Aplikasi',
            'Tips Belajar Efektif',
            'Event Akhir Pekan',
            'Donasi Program HSI',
        ];
        foreach ($bannerTitles as $i => $title) {
            Banner::updateOrCreate(
                ['title' => $title],
                [
                    'slug' => Str::slug($title) . '-' . substr(md5($title), 0, 5),
                    'subtitle' => 'Subjudul untuk ' . $title,
                    'content' => 'Konten banner dummy ' . $title,
                    'image' => null,
                    'is_active' => true,
                    'order' => $i + 1,
                ]
            );
        }

        // Add Questions to Quizzes (Relevant Questions)
        foreach (Quiz::all() as $quiz) {
            if ($quiz->questions()->count() > 0) {
                continue;
            }

            $questions = $this->getQuestionsForCategory($quiz->category);

            foreach ($questions as $index => $qData) {
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $qData['text'],
                    'type' => 'radio',
                    'order' => $index + 1,
                ]);

                foreach ($qData['options'] as $opt) {
                    QuizOption::create([
                        'quiz_question_id' => $question->id,
                        'label' => $opt['label'],
                        'text' => $opt['text'],
                        'is_correct' => $opt['is_correct'],
                    ]);
                }
            }
        }

        // Bank Accounts (6 items for consistency, or keep 3) - User asked 6 for "menu". Bank is not a menu but helper. Keeping 3 or adding more.
        // Let's stick to 3 distinct banks.
        $bankSeeds = [
            ['bank_name' => 'BCA', 'account_number' => '1234567890', 'account_holder' => 'Yayasan HSI', 'is_active' => true],
            ['bank_name' => 'BNI', 'account_number' => '9876543210', 'account_holder' => 'Yayasan HSI', 'is_active' => true],
            ['bank_name' => 'Mandiri', 'account_number' => '5556677889', 'account_holder' => 'Yayasan HSI', 'is_active' => true],
            ['bank_name' => 'BSI', 'account_number' => '7778889990', 'account_holder' => 'Yayasan HSI', 'is_active' => true],
            ['bank_name' => 'BRI', 'account_number' => '1112223334', 'account_holder' => 'Yayasan HSI', 'is_active' => true],
            ['bank_name' => 'Muamalat', 'account_number' => '9990001112', 'account_holder' => 'Yayasan HSI', 'is_active' => true],
        ];
        foreach ($bankSeeds as $b) {
            BankAccount::updateOrCreate(
                ['bank_name' => $b['bank_name'], 'account_number' => $b['account_number']],
                ['account_holder' => $b['account_holder'], 'is_active' => $b['is_active']]
            );
        }
    }

    private function getQuestionsForCategory($category)
    {
        return match ($category) {
            'Aqidah' => [
                [
                    'text' => 'Apa rukun Iman yang pertama?',
                    'options' => [
                        ['label' => 'A', 'text' => 'Iman kepada Allah', 'is_correct' => true],
                        ['label' => 'B', 'text' => 'Iman kepada Malaikat', 'is_correct' => false],
                        ['label' => 'C', 'text' => 'Iman kepada Kitab', 'is_correct' => false],
                        ['label' => 'D', 'text' => 'Iman kepada Rasul', 'is_correct' => false],
                    ]
                ],
                [
                    'text' => 'Dimana Allah berada?',
                    'options' => [
                        ['label' => 'A', 'text' => 'Di mana-mana', 'is_correct' => false],
                        ['label' => 'B', 'text' => 'Di atas Arsy', 'is_correct' => true],
                        ['label' => 'C', 'text' => 'Di hati mukmin', 'is_correct' => false],
                        ['label' => 'D', 'text' => 'Tidak bertempat', 'is_correct' => false],
                    ]
                ],
                // Add more dummy questions...
                 [
                    'text' => 'Apa lawan dari Tauhid?',
                    'options' => [
                        ['label' => 'A', 'text' => 'Syirik', 'is_correct' => true],
                        ['label' => 'B', 'text' => 'Nifaq', 'is_correct' => false],
                        ['label' => 'C', 'text' => 'Fasiq', 'is_correct' => false],
                        ['label' => 'D', 'text' => 'Bidah', 'is_correct' => false],
                    ]
                ],
                 [
                    'text' => 'Siapa pencipta alam semesta?',
                    'options' => [
                        ['label' => 'A', 'text' => 'Malaikat', 'is_correct' => false],
                        ['label' => 'B', 'text' => 'Nabi', 'is_correct' => false],
                        ['label' => 'C', 'text' => 'Allah', 'is_correct' => true],
                        ['label' => 'D', 'text' => 'Manusia', 'is_correct' => false],
                    ]
                ],
                 [
                    'text' => 'Kitab suci umat Islam adalah?',
                    'options' => [
                        ['label' => 'A', 'text' => 'Injil', 'is_correct' => false],
                        ['label' => 'B', 'text' => 'Taurat', 'is_correct' => false],
                        ['label' => 'C', 'text' => 'Zabur', 'is_correct' => false],
                        ['label' => 'D', 'text' => 'Al-Quran', 'is_correct' => true],
                    ]
                ],
            ],
            'Fiqh' => [
                [
                    'text' => 'Apa hukum shalat 5 waktu?',
                    'options' => [
                        ['label' => 'A', 'text' => 'Sunnah', 'is_correct' => false],
                        ['label' => 'B', 'text' => 'Wajib', 'is_correct' => true],
                        ['label' => 'C', 'text' => 'Mubah', 'is_correct' => false],
                        ['label' => 'D', 'text' => 'Makruh', 'is_correct' => false],
                    ]
                ],
                [
                    'text' => 'Air yang suci dan menyucikan disebut air?',
                    'options' => [
                        ['label' => 'A', 'text' => 'Mustamal', 'is_correct' => false],
                        ['label' => 'B', 'text' => 'Mutanajis', 'is_correct' => false],
                        ['label' => 'C', 'text' => 'Mutlaq', 'is_correct' => true],
                        ['label' => 'D', 'text' => 'Musyammas', 'is_correct' => false],
                    ]
                ],
                [
                    'text' => 'Jumlah rakaat shalat Maghrib adalah?',
                    'options' => [
                        ['label' => 'A', 'text' => '2', 'is_correct' => false],
                        ['label' => 'B', 'text' => '3', 'is_correct' => true],
                        ['label' => 'C', 'text' => '4', 'is_correct' => false],
                        ['label' => 'D', 'text' => '1', 'is_correct' => false],
                    ]
                ],
                [
                    'text' => 'Puasa Ramadhan hukumnya?',
                    'options' => [
                        ['label' => 'A', 'text' => 'Sunnah Muakkad', 'is_correct' => false],
                        ['label' => 'B', 'text' => 'Wajib', 'is_correct' => true],
                        ['label' => 'C', 'text' => 'Fardhu Kifayah', 'is_correct' => false],
                        ['label' => 'D', 'text' => 'Haram', 'is_correct' => false],
                    ]
                ],
                [
                    'text' => 'Zakat fitrah dikeluarkan pada bulan?',
                    'options' => [
                        ['label' => 'A', 'text' => 'Muharram', 'is_correct' => false],
                        ['label' => 'B', 'text' => 'Rajab', 'is_correct' => false],
                        ['label' => 'C', 'text' => 'Ramadhan', 'is_correct' => true],
                        ['label' => 'D', 'text' => 'Dzulhijjah', 'is_correct' => false],
                    ]
                ],
            ],
            default => [
                [
                    'text' => 'Pertanyaan umum 1',
                    'options' => [
                        ['label' => 'A', 'text' => 'Opsi A', 'is_correct' => true],
                        ['label' => 'B', 'text' => 'Opsi B', 'is_correct' => false],
                        ['label' => 'C', 'text' => 'Opsi C', 'is_correct' => false],
                        ['label' => 'D', 'text' => 'Opsi D', 'is_correct' => false],
                    ]
                ],
                [
                    'text' => 'Pertanyaan umum 2',
                    'options' => [
                        ['label' => 'A', 'text' => 'Opsi A', 'is_correct' => false],
                        ['label' => 'B', 'text' => 'Opsi B', 'is_correct' => true],
                        ['label' => 'C', 'text' => 'Opsi C', 'is_correct' => false],
                        ['label' => 'D', 'text' => 'Opsi D', 'is_correct' => false],
                    ]
                ],
                 [
                    'text' => 'Pertanyaan umum 3',
                    'options' => [
                        ['label' => 'A', 'text' => 'Opsi A', 'is_correct' => false],
                        ['label' => 'B', 'text' => 'Opsi B', 'is_correct' => false],
                        ['label' => 'C', 'text' => 'Opsi C', 'is_correct' => true],
                        ['label' => 'D', 'text' => 'Opsi D', 'is_correct' => false],
                    ]
                ],
                 [
                    'text' => 'Pertanyaan umum 4',
                    'options' => [
                        ['label' => 'A', 'text' => 'Opsi A', 'is_correct' => false],
                        ['label' => 'B', 'text' => 'Opsi B', 'is_correct' => false],
                        ['label' => 'C', 'text' => 'Opsi C', 'is_correct' => false],
                        ['label' => 'D', 'text' => 'Opsi D', 'is_correct' => true],
                    ]
                ],
                 [
                    'text' => 'Pertanyaan umum 5',
                    'options' => [
                        ['label' => 'A', 'text' => 'Opsi A', 'is_correct' => true],
                        ['label' => 'B', 'text' => 'Opsi B', 'is_correct' => false],
                        ['label' => 'C', 'text' => 'Opsi C', 'is_correct' => false],
                        ['label' => 'D', 'text' => 'Opsi D', 'is_correct' => false],
                    ]
                ],
            ]
        };
    }
}
