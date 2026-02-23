<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $query = Course::with('materials'); // Eager load materials to avoid N+1 and for FE usage
        
        if (in_array($type, ['free','paid'])) {
            $query->where('type', $type);
        }

        $user = auth()->user();
        
        $courses = $query->orderBy('title')->get()->map(function ($c) use ($user) {
            $isEnrolled = false;
            $enrollmentStatus = null;
            
            if ($user && $c->users->contains($user->id)) {
                 $pivot = $c->users->find($user->id)->pivot;
                 $isEnrolled = true;
                 $enrollmentStatus = $pivot->status;
            }

            return [
                'id' => $c->id,
                'title' => $c->title,
                'short_desc' => $c->short_desc,
                'price' => $c->price,
                'type' => $c->type,
                'currency' => $c->currency ?? 'IDR',
                'color' => $c->color,
                'is_enrolled' => $isEnrolled,
                'enrollment_status' => $enrollmentStatus,
                'materials' => $c->materials
            ];
        });

        return response()->json($courses);
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $course = Course::findOrFail($request->course_id);

        // Check if already enrolled
        if ($course->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Already enrolled'], 200);
        }

        $status = 'active';
        if ($course->type === 'paid') {
            $status = 'pending';
        }

        $course->users()->attach($user->id, ['status' => $status]);

        return response()->json([
            'message' => 'Enrolled successfully',
            'status' => $status
        ]);
    }

    public function uploadPayment(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'payment_proof' => 'required|image|max:2048' // 2MB Max
        ]);

        $user = auth()->user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $path = $request->file('payment_proof')->store('payments', 'public');

        // Update Pivot
        $user->courses()->updateExistingPivot($request->course_id, [
            'payment_proof' => $path,
            'status' => 'pending' // Re-affirm pending
        ]);

        return response()->json(['message' => 'Bukti pembayaran berhasil diupload']);
    }

    public function payWithBalance(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);
        
        $user = auth()->user();
        $course = Course::findOrFail($request->course_id);
        
        // 1. Check Balance
        if ($user->balance < $course->price) {
            return response()->json(['message' => 'Saldo tidak mencukupi'], 400);
        }
        
        // 2. Check Enrollment
        $enrollment = $user->courses()->where('course_id', $course->id)->first();
        if(!$enrollment) {
            // If not enrolled yet, enroll them
             $user->courses()->attach($course->id, ['status' => 'active']);
        } else {
             // If already enrolled (pending), update status
             $user->courses()->updateExistingPivot($course->id, ['status' => 'active']);
        }
        
        // 3. Deduct Balance
        $user->decrement('balance', $course->price);
        
        // 4. Record Transaction (Optional, but good practice if we had Transaction model)
        // Transaction::create([...]);
        
        return response()->json(['message' => 'Pembayaran berhasil']);
    }
}
