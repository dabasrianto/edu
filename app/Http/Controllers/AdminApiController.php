<?php

namespace App\Http\Controllers;

use App\Models\AiSetting;
use App\Models\Order;
use App\Models\Post;
use App\Models\Quiz;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Deposit;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminApiController extends Controller
{
    /**
     * Middleware check: ensure admin
     */
    private function checkAdmin()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Unauthorized');
        }
    }

    // =======================================
    // DASHBOARD STATS
    // =======================================
    public function dashboardStats()
    {
        $this->checkAdmin();

        return response()->json([
            'total_users' => User::count(),
            'total_enrollments' => Enrollment::count(),
            'pending_enrollments' => Enrollment::where('status', 'pending')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_posts' => Post::where('type', 'article')->count(),
            'total_quizzes' => Quiz::count(),
            'total_deposits' => Deposit::where('status', 'approved')->sum('amount'),
            'recent_users' => User::latest()->take(5)->get(['id', 'name', 'email', 'created_at']),
        ]);
    }

    // =======================================
    // ORDERS
    // =======================================
    public function orders(Request $request)
    {
        $this->checkAdmin();

        $query = Order::with(['user:id,name,email', 'items.product:id,name'])
            ->orderBy('created_at', 'desc');

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return response()->json($query->take(50)->get());
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $this->checkAdmin();

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
            'admin_note' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        if ($request->admin_note) {
            $order->admin_note = $request->admin_note;
        }
        $order->save();

        return response()->json(['success' => true, 'message' => 'Status order berhasil diupdate.']);
    }

    // =======================================
    // POSTS / ARTICLES
    // =======================================
    public function posts()
    {
        $this->checkAdmin();

        $posts = Post::where('type', 'article')
            ->with('category:id,name')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get(['id', 'title', 'slug', 'status', 'category_id', 'image', 'show_in_slider', 'created_at']);

        return response()->json($posts);
    }

    public function storePost(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:published,draft',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'content' => $request->content,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'type' => 'article',
            'show_in_slider' => $request->boolean('show_in_slider'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        Post::create($data);

        return response()->json(['success' => true, 'message' => 'Artikel berhasil ditambahkan.']);
    }

    public function updatePost(Request $request, $id)
    {
        $this->checkAdmin();

        $post = Post::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:published,draft,archived',
        ]);

        $post->title = $request->title;
        $post->content = $request->content;
        $post->category_id = $request->category_id;
        $post->status = $request->status;
        $post->show_in_slider = $request->boolean('show_in_slider');

        if ($request->hasFile('image')) {
            $post->image = $request->file('image')->store('posts', 'public');
        }

        $post->save();

        return response()->json(['success' => true, 'message' => 'Artikel berhasil diupdate.']);
    }

    public function deletePost($id)
    {
        $this->checkAdmin();
        Post::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Artikel berhasil dihapus.']);
    }

    // =======================================
    // QUIZZES
    // =======================================
    public function quizzes()
    {
        $this->checkAdmin();

        $quizzes = Quiz::withCount('questions')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return response()->json($quizzes);
    }

    public function updateQuiz(Request $request, $id)
    {
        $this->checkAdmin();

        $quiz = Quiz::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean',
            'show_result' => 'sometimes|boolean',
        ]);

        if ($request->has('title')) $quiz->title = $request->title;
        if ($request->has('is_active')) $quiz->is_active = $request->boolean('is_active');
        if ($request->has('show_result')) $quiz->show_result = $request->boolean('show_result');

        $quiz->save();

        return response()->json(['success' => true, 'message' => 'Quiz berhasil diupdate.']);
    }

    public function deleteQuiz($id)
    {
        $this->checkAdmin();
        Quiz::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Quiz berhasil dihapus.']);
    }

    // =======================================
    // AI SETTINGS
    // =======================================
    public function aiSettings()
    {
        $this->checkAdmin();
        return response()->json(AiSetting::all());
    }

    public function storeAiSetting(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'provider' => 'required|string',
            'api_key' => 'required|string',
            'selected_model' => 'required|string',
            'system_prompt' => 'nullable|string',
            'reference_url' => 'nullable|url',
        ]);

        $setting = AiSetting::create([
            'provider' => $request->provider,
            'api_key' => $request->api_key,
            'selected_model' => $request->selected_model,
            'system_prompt' => $request->system_prompt ?? '',
            'reference_url' => $request->reference_url,
            'is_active' => true,
            'models' => [],
        ]);

        return response()->json(['success' => true, 'message' => 'AI Setting berhasil ditambahkan.']);
    }

    public function updateAiSetting(Request $request, $id)
    {
        $this->checkAdmin();

        $setting = AiSetting::findOrFail($id);

        if ($request->has('is_active')) {
            $setting->is_active = $request->boolean('is_active');
        }
        if ($request->has('provider')) $setting->provider = $request->provider;
        if ($request->has('api_key')) $setting->api_key = $request->api_key;
        if ($request->has('selected_model')) $setting->selected_model = $request->selected_model;
        if ($request->has('system_prompt')) $setting->system_prompt = $request->system_prompt;
        if ($request->has('reference_url')) $setting->reference_url = $request->reference_url;

        $setting->save();

        return response()->json(['success' => true, 'message' => 'AI Setting berhasil diupdate.']);
    }

    public function deleteAiSetting($id)
    {
        $this->checkAdmin();
        AiSetting::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'AI Setting berhasil dihapus.']);
    }

    // =======================================
    // USERS
    // =======================================
    public function users(Request $request)
    {
        $this->checkAdmin();

        $query = User::orderBy('created_at', 'desc');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json($query->take(50)->get(['id', 'name', 'email', 'role', 'is_active', 'balance', 'created_at']));
    }

    public function updateUser(Request $request, $id)
    {
        $this->checkAdmin();

        $user = User::findOrFail($id);

        if ($request->has('is_active')) {
            $user->is_active = $request->boolean('is_active');
        }
        if ($request->has('role')) {
            $user->role = $request->role;
        }

        $user->save();

        return response()->json(['success' => true, 'message' => 'User berhasil diupdate.']);
    }

    public function deleteUser($id)
    {
        $this->checkAdmin();

        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri.'], 400);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
    }

    // =======================================
    // ENROLLMENTS
    // =======================================
    public function enrollments()
    {
        $this->checkAdmin();

        return response()->json(
            Enrollment::with(['user:id,name,email', 'course:id,title'])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get()
        );
    }

    public function updateEnrollmentStatus(Request $request, $id)
    {
        $this->checkAdmin();

        $request->validate([
            'status' => 'required|in:active,pending,rejected',
        ]);

        $enrollment = Enrollment::findOrFail($id);
        $enrollment->status = $request->status;
        $enrollment->save();

        return response()->json(['success' => true, 'message' => 'Status pendaftaran berhasil diupdate.']);
    }

    // =======================================
    // CATEGORIES (helper for post form)
    // =======================================
    public function categories()
    {
        $this->checkAdmin();
        return response()->json(Category::all(['id', 'name']));
    }

    // =======================================
    // DEPOSITS / TOP-UP
    // =======================================
    public function deposits()
    {
        $this->checkAdmin();

        return response()->json(
            Deposit::with('user:id,name', 'bankAccount:id,bank_name')
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get()
        );
    }

    public function updateDeposit(Request $request, $id)
    {
        $this->checkAdmin();

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $deposit = Deposit::findOrFail($id);

        if ($deposit->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Deposit sudah diproses.'], 400);
        }

        $deposit->status = $request->status;
        $deposit->save();

        if ($request->status === 'approved') {
            $deposit->user->increment('balance', $deposit->amount);
        }

        return response()->json(['success' => true, 'message' => 'Deposit berhasil di-' . $request->status . '.']);
    }
}
