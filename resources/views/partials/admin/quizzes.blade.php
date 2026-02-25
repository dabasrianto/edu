<!-- Quiz Management Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 flex items-center text-sm">
            @svg('heroicon-s-rectangle-stack', 'w-4 h-4 mr-2 text-yellow-500')
            Kelola Quiz
        </h3>
        <button onclick="loadQuizzes()" class="text-[10px] text-yellow-600 font-bold bg-yellow-50 px-2 py-1 rounded hover:bg-yellow-100">Refresh</button>
    </div>
    <div class="text-[10px] text-gray-500 italic px-4 py-2 bg-yellow-50/30 border-b border-gray-100">
        Untuk membuat quiz baru dengan soal, gunakan <a href="/admin/quizzes/create" class="text-blue-600 underline font-bold">Panel Admin →</a>
    </div>
    <div id="admin-quiz-list" class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
        <div class="px-3 py-4 text-center text-gray-400 text-xs">Memuat data...</div>
    </div>
</div>
