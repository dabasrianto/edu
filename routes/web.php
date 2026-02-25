<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController as AuthRegisterController;
use App\Http\Controllers\RegisterController as RootRegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OTPController;
// Google Login Routes (Removed)
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\EmailSettingController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductMessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\AiChatController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/leaderboard/load-more', [LeaderboardController::class, 'loadMore'])->name('leaderboard.loadMore');

Route::post('/admin/settings/update', [AppSettingController::class, 'update'])->middleware('auth')->name('admin.settings.update');
Route::post('/admin/email/update', [EmailSettingController::class, 'update'])->middleware('auth')->name('admin.email.update');
Route::post('/admin/email/test', [EmailSettingController::class, 'sendTestEmail'])->middleware('auth')->name('admin.email.test');

Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');
Route::get('/quiz/result/{id}', [QuizController::class, 'show'])->name('quiz.result');

Route::get('/banner/{slug}', [BannerController::class, 'show'])->name('banner.show');
Route::get('/post/{slug}', [PostController::class, 'show'])->name('post.show');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// AI Chat Settings (Public to allow frontend URL resolution)
Route::get('/ai/settings', [AiChatController::class, 'getSettings'])->name('ai.settings');

Route::middleware(['auth'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.store');
    // Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count'); // Moved outside
    Route::get('/cart/data', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/remove', [CartController::class, 'destroy'])->name('cart.remove');
    
    Route::post('/order/checkout', [OrderController::class, 'checkout'])->name('order.checkout');
    Route::post('/order/payment', [OrderController::class, 'uploadPayment'])->name('order.payment');
    Route::post('/order/pay-balance', [OrderController::class, 'payWithBalance'])->name('order.pay-balance');
    
    Route::post('/product/message', [ProductMessageController::class, 'store'])->name('product.message');
    Route::post('/topup/process', [TopUpController::class, 'store'])->name('topup.process');

    // Course Admin Routes
    Route::post('/courses/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    
    // Banner Admin Routes
    Route::post('/admin/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::delete('/admin/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // Profile Routes
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
    Route::get('/profile/chats', [ProfileController::class, 'chats'])->name('profile.chats');
    
    // AI Chat Routes
    Route::post('/ai/chat', [AiChatController::class, 'chat'])->name('ai.chat');
});

// Auth Routes
Route::get('/login', function () {
    return redirect('/?tab=login');
})->name('login.page');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [AuthRegisterController::class, 'showUserForm'])->name('register');
Route::post('/register', [RootRegisterController::class, 'registerUser'])->name('register.store');

Route::get('/admin/register', [AuthRegisterController::class, 'showAdminForm'])->name('admin.register');
Route::post('/admin/register', [AuthRegisterController::class, 'registerAdmin']);

// OTP Routes
Route::post('/otp/verify', [OTPController::class, 'verify'])->name('otp.verify');
Route::post('/otp/resend', [OTPController::class, 'resend'])->name('otp.resend');

// Admin API Routes (Frontpage Admin Tab)
Route::middleware(['auth'])->prefix('admin/api')->name('admin.api.')->group(function () {
    Route::get('/stats', [\App\Http\Controllers\AdminApiController::class, 'dashboardStats'])->name('stats');

    Route::get('/enrollments', [\App\Http\Controllers\AdminApiController::class, 'enrollments'])->name('enrollments');
    Route::put('/enrollments/{id}', [\App\Http\Controllers\AdminApiController::class, 'updateEnrollmentStatus'])->name('enrollments.update');

    Route::get('/orders', [\App\Http\Controllers\AdminApiController::class, 'orders'])->name('orders');
    Route::put('/orders/{id}', [\App\Http\Controllers\AdminApiController::class, 'updateOrderStatus'])->name('orders.update');

    Route::get('/posts', [\App\Http\Controllers\AdminApiController::class, 'posts'])->name('posts');
    Route::post('/posts', [\App\Http\Controllers\AdminApiController::class, 'storePost'])->name('posts.store');
    Route::post('/posts/{id}', [\App\Http\Controllers\AdminApiController::class, 'updatePost'])->name('posts.update');
    Route::delete('/posts/{id}', [\App\Http\Controllers\AdminApiController::class, 'deletePost'])->name('posts.delete');

    Route::get('/quizzes', [\App\Http\Controllers\AdminApiController::class, 'quizzes'])->name('quizzes');
    Route::put('/quizzes/{id}', [\App\Http\Controllers\AdminApiController::class, 'updateQuiz'])->name('quizzes.update');
    Route::delete('/quizzes/{id}', [\App\Http\Controllers\AdminApiController::class, 'deleteQuiz'])->name('quizzes.delete');

    Route::get('/ai-settings', [\App\Http\Controllers\AdminApiController::class, 'aiSettings'])->name('ai');
    Route::post('/ai-settings', [\App\Http\Controllers\AdminApiController::class, 'storeAiSetting'])->name('ai.store');
    Route::put('/ai-settings/{id}', [\App\Http\Controllers\AdminApiController::class, 'updateAiSetting'])->name('ai.update');
    Route::delete('/ai-settings/{id}', [\App\Http\Controllers\AdminApiController::class, 'deleteAiSetting'])->name('ai.delete');

    Route::get('/users', [\App\Http\Controllers\AdminApiController::class, 'users'])->name('users');
    Route::put('/users/{id}', [\App\Http\Controllers\AdminApiController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [\App\Http\Controllers\AdminApiController::class, 'deleteUser'])->name('users.delete');

    Route::get('/categories', [\App\Http\Controllers\AdminApiController::class, 'categories'])->name('categories');

    Route::get('/deposits', [\App\Http\Controllers\AdminApiController::class, 'deposits'])->name('deposits');
    Route::put('/deposits/{id}', [\App\Http\Controllers\AdminApiController::class, 'updateDeposit'])->name('deposits.update');
});

