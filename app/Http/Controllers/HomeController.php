<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\Banner;
use App\Models\AppSetting;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Deposit;
use App\Models\BankAccount;
use App\Models\Post;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Cache Key Prefix
        $cacheTime = 60 * 10; // 10 Minutes

        $banners = Cache::remember('home_banners_v2', $cacheTime, function () {
            $banners = Banner::where('is_active', true)->orderBy('order', 'asc')->get();
            $posts = Post::where('show_in_slider', true)->where('status', 'published')->latest()->get();
            
            // Map posts to a banner-like structure
            $postBanners = $posts->map(function($post) {
                return (object)[
                    'id' => 'post_' . $post->id,
                    'title' => $post->title,
                    'subtitle' => $post->category->name ?? null,
                    'image_url' => $post->image_url,
                    'slug' => $post->slug,
                    'type' => 'post', // To distinguish in blade
                    'order' => $post->order ?? 99,
                ];
            });

            return $banners->map(function($b) { 
                $b->type = 'banner'; 
                return $b; 
            })->concat($postBanners)->sortBy('order')->values();
        });

        // We also need $appSettings for the view
        $appSettings = Cache::remember('app_settings', 60 * 60, function () {
            return AppSetting::firstOrCreate(['key' => 'main_settings']);
        });
        
        // Quizzes (Heavy Query - Cached)
        $quizzes = Cache::remember('home_quizzes', $cacheTime, function () {
            return Quiz::where('is_active', true)
                ->with('questions.options')
                ->orderBy('created_at', 'desc')
                ->take(20) // Limit to 20 for performance
                ->get();
        });
        
        // Get User Attempts (if logged in) - NOT CACHED (User Specific)
        $myAttempts = [];
        $quizHistory = [];
        $pendingTopups = 0;
        $depositHistory = [];

        if (Auth::check()) {
            $user = Auth::user();
            $myAttempts = QuizAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->pluck('quiz_id')
                ->toArray();
                
            $quizHistory = QuizAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->with('quiz')
                ->orderBy('created_at', 'desc')
                ->get();
                
            $pendingTopups = Deposit::where('user_id', $user->id)->where('status', 'pending')->count();

            $depositHistory = Deposit::where('user_id', $user->id)
                ->with('bankAccount')
                ->orderBy('created_at', 'desc')
                ->take(20) // Limit to last 20 transactions
                ->get();
        }

        // Leaderboard Data (Cached)
        $leaderboard = Cache::remember('home_leaderboard', $cacheTime, function () {
            return QuizAttempt::where('status', 'completed')
                ->selectRaw('user_id, sum(score) as total_score')
                ->with('user:id,name,avatar')
                ->groupBy('user_id')
                ->orderBy('total_score', 'desc')
                ->take(13)
                ->get();
        });

        $bankAccounts = Cache::remember('active_bank_accounts', 60 * 60, function () {
            return BankAccount::where('is_active', true)->get();
        });

        // Fetch posts for "Berita Terbaru" (Home)
        $homePostsLimit = isset($appSettings->home_config['posts_limit']) ? (int)$appSettings->home_config['posts_limit'] : 6;
        $homePosts = Cache::remember('home_posts_' . $homePostsLimit, $cacheTime, function () use ($homePostsLimit) {
            return Post::where('status', 'published')
                ->with('category')
                ->latest()
                ->take($homePostsLimit)
                ->get();
        });

        // Fetch posts for Blog Tab
        $blogPostsLimit = isset($appSettings->blog_config['posts_limit']) ? (int)$appSettings->blog_config['posts_limit'] : 20;
        $blogPosts = Cache::remember('blog_posts_' . $blogPostsLimit, $cacheTime, function () use ($blogPostsLimit) {
            return Post::where('status', 'published')
                ->with('category')
                ->latest()
                ->take($blogPostsLimit)
                ->get();
        });

        $productsLimit = isset($appSettings->home_config['products_limit']) ? (int)$appSettings->home_config['products_limit'] : 6;
        $products = Cache::remember('home_products_' . $productsLimit, $cacheTime, function () use ($productsLimit) {
            return Product::where('is_active', true)->latest()->take($productsLimit)->get();
        });

        $quizzesJson = $quizzes;

        // Check if AI chatbot has an active configuration
        $aiChatEnabled = \App\Models\AiSetting::where('is_active', true)->exists();

        return view('app', compact(
            'banners', 
            'appSettings', 
            'quizzes', 
            'quizzesJson',
            'myAttempts', 
            'quizHistory', 
            'leaderboard', 
            'pendingTopups', 
            'bankAccounts', 
            'depositHistory', 
            'homePosts', 
            'blogPosts', 
            'products',
            'aiChatEnabled'
        ));
    }
}
