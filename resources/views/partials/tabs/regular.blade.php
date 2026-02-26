
            <!-- ======================= -->
            <!-- TAB: REGULER (LENGKAP) -->
            <!-- ======================= -->
            <div id="view-reguler" class="tab-content hidden fade-in">

                <!-- Header Reguler -->
                <div class="bg-orange-600 text-white p-6 -mt-1 shadow-inner relative overflow-hidden">
                    <div class="relative z-10">
                        <h2 class="text-xl font-bold">{{ $appSettings->regular_title ?? 'Program Reguler' }}</h2>
                        <p class="text-orange-100 text-xs mt-1">{{ $appSettings->regular_slogan ?? 'Evaluasi pemahamanmu secara berkala.' }}</p>
                    </div>
                    <!-- Dekorasi background -->
                    <div class="absolute right-[-20px] top-[-20px] w-32 h-32 bg-white opacity-5 rounded-full"></div>
                    <div class="absolute right-[40px] bottom-[-30px] w-24 h-24 bg-white opacity-10 rounded-full"></div>
                </div>

                <div class="p-4 space-y-6">

                    <!-- BAGIAN: KUIS TERSEDIA -->
                    <div>
                        <div class="flex items-center justify-between mb-3 px-1">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                <span class="bg-blue-100 text-blue-600 p-1 rounded mr-2">
                                    @svg('heroicon-s-bolt', 'w-4 h-4')
                                </span>
                                Kuis Tersedia
                            </h3>
                            <span
                                class="text-[10px] font-bold text-white bg-red-500 px-2 py-1 rounded-full shadow-sm animate-pulse">2
                                Wajib</span>
                        </div>

                        <div class="space-y-3 md:grid md:grid-cols-2 md:gap-4 md:space-y-0">
                        <div class="space-y-3 md:col-span-2 md:grid md:grid-cols-2 md:gap-4 md:space-y-0">
                            @if(isset($quizzes) && count($quizzes) > 0)
                                @foreach($quizzes as $quiz)
                                <div
                                    class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow relative overflow-hidden">
                                    <!-- Border kiri indikator status based on color -->
                                    @php
                                        $borderColor = match($quiz->color) {
                                            'green' => 'bg-green-500',
                                            'red' => 'bg-red-500',
                                            'yellow' => 'bg-yellow-500',
                                            default => 'bg-blue-500',
                                        };
                                        $badgeBg = match($quiz->color) {
                                            'green' => 'bg-green-50 text-green-700 border-green-100',
                                            'red' => 'bg-red-50 text-red-700 border-red-100',
                                            'yellow' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                                            default => 'bg-blue-50 text-blue-700 border-blue-100',
                                        };
                                    @endphp
                                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $borderColor }}"></div>

                                    <div class="flex justify-between items-start mb-2 pl-2">
                                        <div>
                                            <span
                                                class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $badgeBg }} mb-1 border">
                                                {{ $quiz->category ?? 'Umum' }}
                                            </span>
                                            <h4 class="font-bold text-gray-900 text-sm">{{ $quiz->title }}</h4>
                                        </div>
                                        <div class="flex flex-col items-end">
                                            <span
                                                class="text-xs text-orange-600 font-bold bg-orange-50 px-2 py-1 rounded border border-orange-100">
                                                {{ $quiz->duration_minutes }} Menit
                                            </span>
                                        </div>
                                    </div>

                                    <p class="text-xs text-gray-500 mb-3 pl-2">{{ $quiz->description }}</p>

                                    <div class="border-t border-gray-50 pt-3 flex justify-between items-center pl-2">
                                        <div class="flex items-center text-xs text-gray-500">
                                            @svg('heroicon-s-clock', 'w-3 h-3 mr-1 text-gray-400')
                                            Tipe: <span class="text-gray-700 font-medium ml-1 capitalize">{{ $quiz->type }}</span>
                                        </div>
                                        <!-- Tombol Kerjakan dengan Logic Baru -->
                                        @if(in_array($quiz->id, $myAttempts ?? []))
                                            <button disabled class="bg-green-100 text-green-700 text-xs font-bold px-4 py-2 rounded-lg border border-green-200 cursor-not-allowed flex items-center">
                                                @svg('heroicon-s-check-circle', 'w-4 h-4 mr-1')
                                                Selesai
                                            </button>
                                        @else
                                            <button onclick="startQuiz({{ $quiz->id }})"
                                                class="bg-blue-900 text-white text-xs font-bold px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors shadow-sm active:scale-95 transform">
                                                Kerjakan
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-8 text-gray-400 text-sm">
                                    Belum ada kuis yang tersedia saat ini.
                                </div>
                            @endif
                        </div>
                        </div>
                    </div>

                    <!-- BAGIAN: RIWAYAT / SELESAI -->
                    <div class="opacity-75">
                        <div class="flex items-center justify-between mb-3 px-1 mt-6">
                            <h3 class="text-lg font-bold text-gray-600 flex items-center">
                                <span class="bg-gray-100 text-gray-500 p-1 rounded mr-2">
                                    @svg('heroicon-s-clock', 'w-4 h-4')
                                </span>
                                Riwayat Kuis
                            </h3>
                        </div>

                        <div class="space-y-3">
                            @if(isset($quizHistory) && count($quizHistory) > 0)
                                @foreach($quizHistory as $attempt)
                                <div onclick="openQuizResult({{ $attempt->id }})" class="bg-gray-50 rounded-xl border border-gray-100 p-4 cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-bold text-gray-700 text-sm line-through decoration-gray-400">
                                                {{ $attempt->quiz->title }}
                                            </h4>
                                            <p class="text-[10px] text-gray-400">Selesai: {{ $attempt->completed_at ? $attempt->completed_at->format('d M Y H:i') : '-' }}</p>
                                            <span class="text-[10px] text-blue-600 font-bold underline mt-1 block">Lihat Detail</span>
                                        </div>
                                        <div class="text-right">
                                            <span class="block text-xl font-bold {{ $attempt->score >= 70 ? 'text-green-600' : 'text-orange-500' }}">{{ $attempt->score }}</span>
                                            <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Nilai</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 text-gray-400 text-xs italic">
                                    Belum ada riwayat kuis.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-center pt-2">
                        <p class="text-xs text-gray-400">Semangat menuntut ilmu!</p>
                    </div>

                </div>
            </div>
