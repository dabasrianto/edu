<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function download($attemptId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $attempt = QuizAttempt::with(['quiz.certificateTemplate', 'user'])->findOrFail($attemptId);

        // Security Check
        if ($attempt->user_id !== Auth::id() && !Auth::user()->is_admin) {
             abort(403, 'Unauthorized');
        }

        // Check if certificate is enabled for this quiz
        $threshold = $attempt->quiz->certificate_threshold;
        if (is_null($threshold)) {
             abort(404, 'Certificate not available for this quiz.');
        }

        // Check if passed
        if ($attempt->score < $threshold) {
             abort(403, 'Score validation failed. Minimum score required: ' . $threshold);
        }

        // Fetch Template (Prioritize Quiz specific, fallback to global active)
        $template = $attempt->quiz->certificateTemplate ?? \App\Models\CertificateTemplate::where('is_active', true)->first();
        
        $bgImage = null;
        if($template && $template->background_image) {
            $path = storage_path('app/public/' . $template->background_image);
            if(file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $dataImg = file_get_contents($path);
                $bgImage = 'data:image/' . $type . ';base64,' . base64_encode($dataImg);
            }
        }

        // Data for PDF
        $data = [
            'user' => $attempt->user,
            'quiz' => $attempt->quiz,
            'score' => $attempt->score,
            'date' => $attempt->completed_at ? $attempt->completed_at->format('d F Y') : now()->format('d F Y'),
            'certificate_id' => 'HSI-CERT-' . str_pad($attempt->id, 6, '0', STR_PAD_LEFT),
            'bgImage' => $bgImage,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('certificates.default', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Sertifikat-' . $attempt->quiz->title . '.pdf');
    }
}
