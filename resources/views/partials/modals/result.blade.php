
    <!-- QUIZ RESULT MODAL -->
    <div id="result-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg flex flex-col relative overflow-hidden shadow-2xl animate-scale-up" style="max-height: 90vh;">
            <!-- Header -->
            <div class="p-4 border-b flex justify-between items-center bg-gray-50 z-10">
                 <div>
                     <h3 class="font-bold text-lg text-gray-900">Detail Hasil Kuis</h3>
                     <p class="text-xs text-gray-500 mt-1" id="result-score-display">Nilai: -</p>
                 </div>
                 <button onclick="document.getElementById('result-modal').classList.add('hidden')" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                    @svg('heroicon-o-x-mark', 'w-6 h-6')
                 </button>
            </div>
            
            <!-- Body -->
            <div id="result-content" class="flex-1 overflow-y-auto p-6 space-y-8 bg-white">
                 <!-- Results injected here -->
            </div>
            
            <!-- Footer -->
            <div class="p-4 border-t bg-gray-50 flex justify-end items-center z-10">
                 <button onclick="document.getElementById('result-modal').classList.add('hidden')" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-bold text-sm hover:bg-gray-300 transition-colors">
                    Tutup
                 </button>
            </div>
        </div>
    </div>
