
    <div id="proof-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeProofModal()"></div>
        <div class="relative bg-white rounded-2xl max-w-lg w-full p-2 animate-scale-up z-10">
            <button onclick="closeProofModal()" class="absolute -top-10 right-0 text-white hover:text-gray-200">
                @svg('heroicon-o-x-mark', 'w-8 h-8')
            </button>
            <img id="proof-image" src="" class="w-full h-auto rounded-xl shadow-lg border bg-gray-100">
            <div class="mt-2 text-center">
                <a id="proof-download-link" href="#" target="_blank" class="inline-block text-sm text-blue-600 hover:underline">
                    Buka Gambar Penuh
                </a>
            </div>
        </div>
    </div>
