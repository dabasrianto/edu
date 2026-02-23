
            <!-- ======================= -->
            <!-- TAB: AKADEMI (LENGKAP)  -->
            <!-- ======================= -->
            <div id="view-akademi" class="tab-content hidden fade-in">

                <!-- Banner Kecil Akademi -->
                <div class="bg-blue-800 text-white p-6 -mt-1 shadow-inner relative overflow-hidden">
                    <div class="relative z-10">
                        <h2 class="text-xl font-bold">{{ $appSettings->academy_title ?? 'Akademi HSI' }}</h2>
                        <p class="text-blue-200 text-xs mt-1">{{ $appSettings->academy_slogan ?? 'Tuntutlah ilmu dari buaian hingga liang lahat.' }}</p>
                    </div>
                    <div class="absolute right-[-20px] top-[-20px] w-32 h-32 bg-white opacity-5 rounded-full"></div>
                    <div class="absolute right-[40px] bottom-[-30px] w-24 h-24 bg-white opacity-10 rounded-full"></div>
                </div>

                <div class="p-4 space-y-6" id="akademi-container">
                    <!-- Data will be loaded here via JS -->
                    <div class="text-center py-10">
                        @svg('heroicon-o-arrow-path', 'animate-spin h-8 w-8 text-blue-900 mx-auto mb-2')
                        <p class="text-gray-500 text-sm">Memuat program...</p>
                    </div>
                </div>
            </div>
