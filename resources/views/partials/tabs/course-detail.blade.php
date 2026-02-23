
            <!-- ======================= -->
            <!-- VIEW: COURSE DETAIL     -->
            <!-- ======================= -->
            <div id="view-course-detail" class="hidden slide-in-right bg-white min-h-full pb-20 max-w-md mx-auto">
                <!-- Navigasi Back -->
                <div
                    class="sticky top-0 bg-white z-20 border-b border-gray-100 px-4 py-3 flex items-center space-x-3 shadow-sm">
                    <button onclick="closeCourseDetail()" class="p-2 rounded-full hover:bg-gray-100 transition-colors cursor-pointer relative z-50">
                        @svg('heroicon-o-arrow-left', 'w-5 h-5 text-gray-700')
                    </button>
                    <h2 class="font-bold text-gray-900 text-lg">Detail Program</h2>
                </div>

                <!-- Hero Section (Dinamis) -->
                <div class="p-4">
                    <!-- Icon Placeholder -->
                    <div id="detail-bg-color"
                        class="w-full h-40 bg-blue-50 rounded-2xl flex items-center justify-center mb-4">
                        @svg('heroicon-o-book-open', 'w-16 h-16 text-blue-500 opacity-50')
                    </div>

                    <div class="mb-6">
                        <span id="detail-tag"
                            class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 mb-2">Pendaftaran
                            Dibuka</span>
                        <h1 id="detail-title" class="text-2xl font-bold text-gray-900 mb-2">Judul Program</h1>
                        <p id="detail-price" class="text-xl font-bold text-orange-600 mb-4">Gratis</p>
                        <p id="detail-desc" class="text-gray-600 text-sm leading-relaxed">
                            Deskripsi lengkap mengenai program ini akan ditampilkan di sini. Program ini dirancang untuk
                            memudahkan peserta memahami materi secara komprehensif.
                        </p>
                    </div>

                    <!-- Material Player (Hidden by default) -->
                    <div id="material-player-container" class="hidden mb-6 bg-black rounded-xl overflow-hidden shadow-lg transition-all duration-300">
                         <div class="relative pt-[56.25%] bg-gray-900 group">
                             <!-- Iframe/Video injected here -->
                             <iframe id="player-iframe" class="absolute inset-0 w-full h-full z-10" src="" frameborder="0" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                             <div id="player-placeholder" class="absolute inset-0 flex items-center justify-center text-white z-0">
                                 <div class="text-center">
                                     @svg('heroicon-o-arrow-path', 'animate-spin h-8 w-8 text-white mx-auto mb-2')
                                     <p class="text-xs text-gray-400">Memuat Media...</p>
                                 </div>
                             </div>
                         </div>
                         <div class="p-4 bg-gray-800 text-white flex justify-between items-center">
                             <div>
                                 <h3 id="player-title" class="font-bold text-sm tracking-wide">Judul Materi</h3>
                                 <p id="player-timer" class="text-xs text-yellow-400 mt-1 hidden font-mono flex items-center">
                                    @svg('heroicon-s-clock', 'w-3 h-3 mr-1')
                                    Wajib tonton: <span id="timer-countdown" class="font-bold text-white mx-0.5">0</span> detik
                                 </p>
                             </div>
                             <button onclick="closePlayer()" class="text-xs bg-gray-700 hover:bg-gray-600 px-3 py-1.5 rounded transition-colors border border-gray-600">Tutup</button>
                         </div>
                    </div>

                    <!-- Tabs/Section Materi -->
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                            @svg('heroicon-o-document-text', 'w-5 h-5 mr-2 text-gray-500')
                            Materi Pembelajaran
                        </h3>
                        <div class="space-y-2">
                           <!-- Injected JS Logic -->
                        </div>
                    </div>
                </div>

                <!-- Sticky Footer Button -->
                <div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto p-4 bg-white border-t border-gray-100 z-30">
                    <button id="btn-enroll-action" onclick="handleEnrollClick()"
                        class="w-full bg-blue-900 text-white font-bold py-3.5 rounded-xl hover:bg-blue-800 transition-colors shadow-lg shadow-blue-900/20 active:scale-[0.98] transform transition-transform">
                        Daftar Sekarang
                    </button>
                </div>
            </div>
