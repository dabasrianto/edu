
    <!-- QUIZ MODAL -->
    <div id="quiz-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/90 backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-lg flex flex-col relative overflow-hidden shadow-2xl animate-scale-up" style="max-height: 90vh;">
            <!-- Header -->
            <div class="p-4 border-b flex justify-between items-center bg-gray-50 z-10">
                 <div>
                     <h3 id="quiz-modal-title" class="font-bold text-lg text-gray-900">Quiz Title</h3>
                     <p class="text-xs text-gray-500 flex items-center mt-1">
                        @svg('heroicon-s-clock', 'w-3 h-3 mr-1 text-gray-400')
                        Sisa Waktu: <span id="quiz-timer" class="font-mono font-bold text-red-600 ml-1">00:00</span>
                     </p>
                 </div>
                 <button onclick="closeQuizModal()" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                    @svg('heroicon-o-x-mark', 'w-6 h-6')
                 </button>
            </div>
            
            <!-- Body -->
            <div id="quiz-questions-container" class="flex-1 overflow-y-auto p-6 space-y-8 bg-white">
                 <!-- Questions injected here -->
            </div>
            
            <!-- Footer -->
            <div class="p-4 border-t bg-gray-50 flex justify-end items-center space-x-3 z-10">
                 <button onclick="closeQuizModal()" class="text-gray-500 font-bold text-sm px-4 py-2 hover:bg-gray-100 rounded-lg">Batal</button>
                 <button onclick="submitQuiz()" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold text-sm shadow-lg hover:bg-blue-800 transition-colors active:scale-95 transform">
                    Kirim Jawaban
                 </button>
            </div>
        </div>
    </div>
