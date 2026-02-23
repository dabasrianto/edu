<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('materials')->get();

        if (auth()->check()) {
            $user = auth()->user();
            // Assuming 'enrollments' or 'users' relationship exists. 
            // Based on Model Course, it has 'users' (belongsToMany) and 'enrollments' (hasMany)
            // Let's use 'enrollments' if it tracks individual enrollment records with status
            
            // Or easier, fetch user's enrollments separately
            $enrollments = \App\Models\Enrollment::where('user_id', $user->id)->get()->keyBy('course_id');

            $courses->transform(function ($course) use ($enrollments) {
                $enrollment = $enrollments->get($course->id);
                $course->is_enrolled = $enrollment ? true : false;
                $course->enrollment_status = $enrollment ? $enrollment->status : null;
                return $course;
            });
        } else {
             $courses->transform(function ($course) {
                $course->is_enrolled = false;
                $course->enrollment_status = null;
                return $course;
            });
        }

        return response()->json($courses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'short_desc' => 'required|string|max:255',
            'type' => 'required|in:free,paid',
            'price' => 'nullable|numeric|min:0',
            'color' => 'required|string',
        ]);

        Course::create([
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title) . '-' . uniqid(),
            'short_desc' => $request->short_desc,
            'type' => $request->type,
            'price' => $request->price ?? 0,
            'currency' => 'IDR',
            'color' => $request->color,
        ]);

        return back()->with('success', 'Kursus berhasil ditambahkan!');
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = auth()->user();
        $course = Course::findOrFail($request->course_id);

        // Check if already enrolled
        $existing = \App\Models\Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Anda sudah terdaftar di kursus ini.',
                'status' => $existing->status
            ]);
        }

        // Create enrollment
        $status = ($course->price > 0) ? 'pending' : 'active';
        
        \App\Models\Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Pendaftaran berhasil.',
            'status' => $status
        ]);
    }

    public function destroy($id)
    {
        Course::findOrFail($id)->delete();
        return back()->with('success', 'Kursus berhasil dihapus!');
    }
}
