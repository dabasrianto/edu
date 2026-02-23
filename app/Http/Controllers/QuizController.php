<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;

class QuizController extends Controller
{
    public function submit(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'score' => 'required|integer',
            'answers' => 'required|array', // Must be array of {question_id: x, option_id: y}
            'answers.*.question_id' => 'required|exists:quiz_questions,id',
            'answers.*.option_id' => 'required|exists:quiz_options,id',
        ]);

        // Check if already attempted?
        $exists = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $data['quiz_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah mengerjakan kuis ini.'], 403);
        }

        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $data['quiz_id'],
            'score' => $data['score'],
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        foreach ($data['answers'] as $ans) {
            QuizAttemptAnswer::create([
                'quiz_attempt_id' => $attempt->id,
                'quiz_question_id' => $ans['question_id'],
                'quiz_option_id' => $ans['option_id'],
            ]);
        }

        return response()->json(['message' => 'Nilai berhasil disimpan.']);
    }

    public function show($id)
    {
        if (!Auth::check()) return response()->json(['message' => 'Unauthorized'], 401);

        $attempt = QuizAttempt::with(['quiz.questions.options', 'answers'])->findOrFail($id);
        
        // Authorization check
        if ($attempt->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        // Visibility check
        if (!$attempt->quiz->show_result) {
            return response()->json(['message' => 'Hasil belum dirilis.'], 403);
        }

        return response()->json($attempt);
    }
}
