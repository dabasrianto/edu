<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function loadMore(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 10;
        
        // Offset by 3 because top 3 are in podium, and we already showed $perPage (initial limit) - 3?
        // Actually, the initial load in web.php will take e.g. 13 (3 podium + 10 list).
        // So this loadMore should start after that.
        
        // Let's standardise:
        // Initial load: Top 13 (3 podium + 10 list).
        // Load More: Skip 13, Take 10.
        
        $offset = 13 + (($page - 1) * $perPage);
        
        $ranks = QuizAttempt::where('status', 'completed')
            ->selectRaw('user_id, sum(score) as total_score')
            ->with('user:id,name,avatar')
            ->groupBy('user_id')
            ->orderBy('total_score', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get();
            
        // Calculate dynamic rank index
        $startRank = $offset + 1;

        $html = '';
        foreach ($ranks as $index => $rank) {
            $currentRank = $startRank + $index;
            $html .= view('partials.leaderboard-item', compact('rank', 'currentRank'))->render();
        }

        return response()->json([
            'html' => $html,
            'hasMore' => $ranks->count() === $perPage
        ]);
    }
}
