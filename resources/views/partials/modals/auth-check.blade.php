
    <!-- AUTH CHECK MODAL -->
    <div id="auth-check-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeAuthModal()"></div>
        <div class="relative bg-white rounded-2xl max-w-xs w-full p-6 animate-scale-up z-10 text-center">
            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                @svg('heroicon-s-lock-closed', 'w-8 h-8 text-orange-500')
            </div>
            <h3 class="text-lg font-bold mb-2 text-gray-900">Akses Terbatas</h3>
            <p class="text-sm text-gray-500 mb-6">Anda harus login atau daftar terlebih dahulu untuk mengambil kursus ini.</p>
            
            <div class="space-y-3">
                 <button onclick="closeAuthModal(); closeCourseDetail(); switchTab('login')" class="w-full bg-blue-900 text-white font-bold py-3 rounded-xl hover:bg-blue-800 transition-colors shadow-lg active:scale-95 transform">
                    Login Sekarang
                </button>
                 <button onclick="closeAuthModal()" class="w-full text-gray-400 font-medium text-xs hover:text-gray-600 mt-2">
                    Nanti Saja
                </button>
            </div>
        </div>
        <script>
            function closeAuthModal() {
                document.getElementById('auth-check-modal').classList.add('hidden');
            }
            function openAuthModal() {
                document.getElementById('auth-check-modal').classList.remove('hidden');
            }
        </script>
    </div>
