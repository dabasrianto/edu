
            <!-- ======================= -->
            <!-- TAB: BERANDA (HOME)     -->
            <!-- ======================= -->
            <div id="view-home" class="tab-content {{ (request('tab') == 'home' || !request('tab')) ? '' : 'hidden' }} fade-in">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 shadow-sm mb-2" role="alert">
                        <strong class="font-bold">Sukses!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if(isset($pendingTopups) && $pendingTopups > 0)
                    <div class="bg-orange-50 border-l-4 border-orange-400 p-4 shadow-sm mb-2">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                @svg('heroicon-s-clock', 'h-5 w-5 text-orange-400')
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-orange-700">
                                    Anda memiliki <span class="font-bold">{{ $pendingTopups }} top-up pending</span>. Mohon tunggu konfirmasi admin.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                <!-- Header Beranda -->
                <div class="bg-emerald-700 text-white p-6 -mt-1 shadow-inner relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="text-emerald-100 text-xs mt-1">{{ $appSettings->home_config['greeting'] ?? 'Assalamualaikum,' }}</p>
                        @auth
                            <h2 class="text-xl font-bold">{{ Auth::user()->name }}</h2>
                            <p class="text-emerald-100 text-xs mt-1">{{ $appSettings->home_config['balance_label'] ?? 'Saldo' }}: Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}</p>
                        @else
                            <h2 class="text-xl font-bold">Tamu</h2>
                            <p class="text-emerald-100 text-xs mt-1">Silakan Login untuk melanjutkan</p>
                        @endauth
                    </div>
                    <!-- Dekorasi background (sama persis dgn Akademi & Reguler) -->
                    <div class="absolute right-[-20px] top-[-20px] w-32 h-32 bg-white opacity-5 rounded-full"></div>
                    <div class="absolute right-[40px] bottom-[-30px] w-24 h-24 bg-white opacity-10 rounded-full"></div>
                </div>
                <script>document.addEventListener('DOMContentLoaded', () => { if(window.updateCartBadge) window.updateCartBadge(); });</script>

                <!-- Bagian Slider/Carousel -->
                <!-- Bagian Slider/Carousel -->
                <section class="bg-white pt-4 pb-5 shadow-sm">
                    <div id="banner-slider"
                        class="flex overflow-x-auto no-scrollbar px-4 space-x-3 md:space-x-4 scroll-smooth snap-x snap-mandatory">
                        <!-- Dynamic Banners from Database -->
                        @if(isset($banners) && count($banners) > 0)
                            @foreach($banners as $banner)
                            @php
                                $link = ($banner->type ?? 'banner') === 'post' 
                                    ? route('post.show', $banner->slug) 
                                    : route('banner.show', $banner->slug);
                            @endphp
                            <a href="{{ $link }}" class="flex-shrink-0 w-[85%] md:w-[60%] lg:w-[45%] shadow-lg rounded-lg block snap-center relative">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                    class="rounded-lg w-full object-cover aspect-[2/1] md:aspect-[16/7]">
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 rounded-b-lg">
                                    <h3 class="text-white font-bold text-sm md:text-base line-clamp-2">{{ $banner->title }}</h3>
                                    @if($banner->subtitle)
                                    <p class="text-gray-200 text-xs md:text-sm line-clamp-1">{{ $banner->subtitle }}</p>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        @else
                            <!-- Fallback/Empty State -->
                            <div class="flex-shrink-0 w-[85%] md:w-[60%] bg-gray-100 rounded-lg flex items-center justify-center aspect-[2/1] md:aspect-[16/7] snap-center">
                                <p class="text-gray-400 text-xs md:text-sm">Belum ada banner terbaru.</p>
                            </div>
                        @endif
                    </div>
                    <!-- Dots Indicator -->
                    <div class="flex justify-center mt-3 space-x-1 md:space-x-1.5" id="slider-dots">
                        @php $slideCount = isset($banners) && count($banners) > 0 ? count($banners) : 1; @endphp
                        @for($i = 0; $i < $slideCount; $i++)
                            <div class="h-2 md:h-2.5 rounded-full transition-all duration-300 {{ $i == 0 ? 'w-4 md:w-5 bg-blue-900' : 'w-2 md:w-3 bg-gray-300' }}"></div>
                        @endfor
                    </div>
                </section>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const slider = document.getElementById('banner-slider');
                        const dotsContainer = document.getElementById('slider-dots');
                        if (!slider || !dotsContainer) return;

                        const dots = dotsContainer.children;
                        let isDown = false;
                        let isPaused = false;
                        let startX;
                        let scrollLeft;
                        let lastX;
                        let autoScrollTimer;

                        // Tambahkan CSS agar gambar tidak bisa ditarik (anti-ghosting)
                        slider.style.userSelect = 'none';
                        slider.querySelectorAll('img').forEach(img => {
                            img.setAttribute('draggable', 'false');
                        });

                        const updateDots = () => {
                            if (!dots.length) return;
                            const firstCard = slider.querySelector('a') || slider.firstElementChild;
                            if (!firstCard) return;
                            const slideWidth = firstCard.offsetWidth + 12;
                            const index = Math.round(slider.scrollLeft / slideWidth);

                            Array.from(dots).forEach((dot, i) => {
                                dot.className = (i === index) 
                                    ? 'h-2 md:h-2.5 rounded-full transition-all duration-300 w-4 md:w-5 bg-blue-900' 
                                    : 'h-2 md:h-2.5 rounded-full transition-all duration-300 w-2 md:w-3 bg-gray-300';
                            });
                        };

                        slider.addEventListener('scroll', updateDots);

                        // LOGIKA KLIK DAN SERET (DRAG)
                        slider.addEventListener('mousedown', (e) => {
                            if (e.button !== 0) return; // Hanya klik kiri
                            isDown = true;
                            isPaused = true;
                            slider.classList.add('cursor-grabbing'); 
                            slider.style.scrollBehavior = 'auto'; // Matikan animasi halus saat dragging
                            slider.style.scrollSnapType = 'none';  // Matikan snapping agar tidak kaku
                            
                            startX = e.pageX - slider.offsetLeft;
                            scrollLeft = slider.scrollLeft;
                            lastX = e.pageX;
                        });

                        const endDrag = (e) => {
                            if (!isDown) return;
                            isDown = false;
                            isPaused = false;
                            slider.classList.remove('cursor-grabbing');
                            slider.style.scrollBehavior = 'smooth';
                            slider.style.scrollSnapType = 'x mandatory';
                        };

                        slider.addEventListener('mouseleave', endDrag);
                        slider.addEventListener('mouseup', endDrag);

                        slider.addEventListener('mousemove', (e) => {
                            if (!isDown) return;
                            e.preventDefault();
                            
                            const x = e.pageX - slider.offsetLeft;
                            const walk = (x - startX) * 1.5; // Walk speed multiplier
                            slider.scrollLeft = scrollLeft - walk;
                        });

                        // Mencegah klik link saat sedang menyeret
                        slider.querySelectorAll('a').forEach(link => {
                            link.addEventListener('click', (e) => {
                                if (isDown || Math.abs(e.pageX - lastX) > 5) {
                                    e.preventDefault();
                                }
                            });
                            // Catat posisi awal untuk deteksi seret vs klik
                            link.addEventListener('mousedown', (e) => lastX = e.pageX);
                        });

                        // Auto Play
                        const startAutoScroll = () => {
                            clearInterval(autoScrollTimer);
                            autoScrollTimer = setInterval(() => {
                                if (isPaused || isDown) return;
                                const firstCard = slider.firstElementChild;
                                if (!firstCard) return;
                                const maxScroll = slider.scrollWidth - slider.clientWidth;
                                if (slider.scrollLeft >= maxScroll - 10) {
                                    slider.scrollTo({ left: 0, behavior: 'smooth' });
                                } else {
                                    slider.scrollBy({ left: firstCard.offsetWidth + 12, behavior: 'smooth' });
                                }
                            }, 4500);
                        };

                        slider.addEventListener('mouseenter', () => isPaused = true);
                        slider.addEventListener('mouseleave', () => { if(!isDown) isPaused = false; });
                        
                        startAutoScroll();
                    });
                </script>
                <style>
                    #banner-slider.cursor-grabbing { cursor: grabbing !important; }
                    #banner-slider.cursor-grabbing a { pointer-events: none; }
                </style>



                <!-- Bagian Berita Terbaru -->
                <section class="px-4 py-5">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900">Berita Terbaru</h3>
                        <!-- <a href="#" class="text-sm text-blue-600 font-semibold">Lihat Semua</a> -->
                    </div>
                    <div class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 md:gap-4">
                        @if(isset($homePosts) && count($homePosts) > 0)
                            @foreach($homePosts as $post)
                                @include('partials.post-item')
                            @endforeach
                        @else
                             <div class="text-center py-6 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                                <p class="text-gray-400 text-sm md:text-base">Belum ada berita terbaru.</p>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Bagian Tagihan Pembelajaran -->
                <section class="px-4 py-5">
                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-3">{{ $appSettings->home_config['bill_title'] ?? 'Tagihan Pembelajaran' }}</h3>
                    <div id="bill-section-list" class="space-y-3">
                        <div class="bg-gray-50 text-gray-400 p-4 rounded-lg text-center text-xs animate-pulse">
                            Memuat data tagihan...
                        </div>
                    </div>
                </section>

                <!-- Bagian Info Pendaftaran -->
                <section class="px-4 py-5">
                    <h3 class="text-xl md:text-2xl font-bold text-gray-900 mb-3">{{ $appSettings->home_config['reg_info_title'] ?? 'Info Pendaftaran' }}</h3>
                    <div class="space-y-4">
                        @php
                            $infoBlocks = $appSettings->home_config['info_blocks'] ?? [];
                            // Fallback for backward compatibility if no blocks defined but old single fields exist
                            if (empty($infoBlocks) && !empty($appSettings->home_config['reg_title'])) {
                                $infoBlocks[] = [
                                    'title' => $appSettings->home_config['reg_title'],
                                    'body' => $appSettings->home_config['reg_body'] ?? '',
                                    'status' => $appSettings->home_config['reg_status'] ?? '',
                                    'action_type' => $appSettings->home_config['reg_action_type'] ?? 'none',
                                    'action_label' => $appSettings->home_config['reg_action_label'] ?? '',
                                    'action_url' => $appSettings->home_config['reg_action_url'] ?? '',
                                ];
                            }
                        @endphp

                        @foreach($infoBlocks as $block)
                        <div class="bg-white rounded-lg shadow-md p-4 md:p-5">
                            <div class="bg-blue-50 text-blue-800 p-3 md:p-4 rounded-lg flex items-center space-x-2">
                                @svg('heroicon-s-pencil-square', 'w-6 h-6 flex-shrink-0')
                                <span class="font-bold text-sm md:text-base uppercase">{{ $block['title'] ?? 'Info Pendaftaran' }}</span>
                            </div>
                            <div class="mt-4 text-gray-700 text-sm md:text-base">
                                <p class="whitespace-pre-line">{{ $block['body'] ?? '' }}</p>
                                
                                <div class="flex justify-between items-center mt-3 border-t border-gray-100 pt-3">
                                    <strong class="font-bold text-gray-900 block text-xs md:text-sm">{{ $block['status'] ?? '' }}</strong>
                                    
                                    @php
                                        $bActionType = $block['action_type'] ?? 'none';
                                        $bActionLabel = $block['action_label'] ?? 'Lihat Detail';
                                        $bActionUrl = $block['action_url'] ?? '#';
                                        
                                        $bOnclick = '';
                                        if($bActionType === 'tab_akademi') $bOnclick = "switchTab('akademi')";
                                        elseif($bActionType === 'tab_reguler') $bOnclick = "switchTab('reguler')";
                                        elseif($bActionType === 'url') $bOnclick = "window.open('$bActionUrl', '_blank')";
                                    @endphp

                                    @if($bActionType !== 'none')
                                    <button onclick="{{ $bOnclick }}" class="bg-blue-600 text-white text-xs md:text-sm font-bold px-3 py-1.5 md:px-4 md:py-2 rounded-lg hover:bg-blue-700 transition-colors shadow-sm flex items-center animate-pulse">
                                        {{ $bActionLabel }}
                                        @svg('heroicon-s-arrow-right', 'w-3 h-3 ml-1')
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>

                <!-- Bagian Leaderboard / Peringkat -->
                <section class="px-4 pb-32">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        @svg('heroicon-s-trophy', 'w-6 h-6 text-yellow-500 mr-2')
                        Peringkat Belajar
                    </h3>
                    
                    @if(isset($leaderboard) && count($leaderboard) > 0)
                    <div class="bg-gradient-to-br from-indigo-900 to-blue-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden mb-6">
                        <!-- Decorative Circles -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-10 -mt-10"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-5 rounded-full -ml-8 -mb-8"></div>

                        <!-- Top 3 Podium -->
                        <div class="flex justify-center items-end space-x-2 mb-2">
                             <!-- Rank 2 -->
                            @if(isset($leaderboard[1]))
                            <div class="flex flex-col items-center w-1/3 z-10">
                                <div class="w-14 h-14 rounded-full border-2 border-slate-300 bg-slate-200 overflow-hidden mb-2 shadow-lg relative">
                                    <img src="{{ $leaderboard[1]->user->avatar ? Storage::url($leaderboard[1]->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($leaderboard[1]->user->name).'&background=cbd5e1&color=475569' }}" alt="Rank 2" class="w-full h-full object-cover">
                                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 bg-slate-500 text-white text-[10px] font-bold px-1.5 rounded-full border border-white">2</div>
                                </div>
                                <p class="text-xs font-semibold text-center line-clamp-1 w-full text-slate-200">{{ $leaderboard[1]->user->name }}</p>
                                <p class="text-[10px] text-slate-300">{{ $leaderboard[1]->total_score }} Poin</p>
                                <div class="h-16 w-full bg-slate-400/30 rounded-t-lg mt-2 mx-auto"></div>
                            </div>
                            @endif

                            <!-- Rank 1 -->
                            @if(isset($leaderboard[0]))
                            <div class="flex flex-col items-center w-1/3 z-20 -mx-2">
                                <div class="relative">
                                    <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 text-2xl">👑</div>
                                    <div class="w-20 h-20 rounded-full border-4 border-yellow-400 bg-yellow-100 overflow-hidden mb-2 shadow-xl relative">
                                        <img src="{{ $leaderboard[0]->user->avatar ? Storage::url($leaderboard[0]->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($leaderboard[0]->user->name).'&background=fef3c7&color=d97706' }}" alt="Rank 1" class="w-full h-full object-cover">
                                        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 bg-yellow-500 text-white text-xs font-bold px-2 py-0.5 rounded-full border-2 border-white">1</div>
                                    </div>
                                </div>
                                <p class="text-sm font-bold text-center line-clamp-1 w-full text-yellow-100">{{ $leaderboard[0]->user->name }}</p>
                                <p class="text-xs text-yellow-200">{{ $leaderboard[0]->total_score }} Poin</p>
                                <div class="h-24 w-full bg-gradient-to-t from-yellow-500/50 to-yellow-400/20 rounded-t-lg mt-2 mx-auto border-t border-yellow-400/30"></div>
                            </div>
                            @endif

                            <!-- Rank 3 -->
                            @if(isset($leaderboard[2]))
                            <div class="flex flex-col items-center w-1/3 z-10">
                                <div class="w-14 h-14 rounded-full border-2 border-orange-300 bg-orange-100 overflow-hidden mb-2 shadow-lg relative">
                                    <img src="{{ $leaderboard[2]->user->avatar ? Storage::url($leaderboard[2]->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($leaderboard[2]->user->name).'&background=ffedd5&color=c2410c' }}" alt="Rank 3" class="w-full h-full object-cover">
                                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 bg-orange-600 text-white text-[10px] font-bold px-1.5 rounded-full border border-white">3</div>
                                </div>
                                <p class="text-xs font-semibold text-center line-clamp-1 w-full text-orange-200">{{ $leaderboard[2]->user->name }}</p>
                                <p class="text-[10px] text-orange-300">{{ $leaderboard[2]->total_score }} Poin</p>
                                <div class="h-12 w-full bg-orange-500/30 rounded-t-lg mt-2 mx-auto"></div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- List Rank 4-10+ -->
                    <div id="leaderboard-list" class="space-y-3">
                        @foreach($leaderboard->slice(3) as $index => $rank)
                            @include('partials.leaderboard-item', ['rank' => $rank, 'currentRank' => $index + 4])
                        @endforeach
                    </div>
                    
                    <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        let page = 1;
                        let loading = false;
                        let hasMore = true;
                        const container = document.getElementById('leaderboard-list');
                        const loadingIndicator = document.getElementById('leaderboard-loading');

                        if(!container) return;

                        const loadMoreLeaderboard = () => {
                            if (loading || !hasMore) return;
                            loading = true;
                            loadingIndicator.classList.remove('hidden');

                            page++;
                            fetch(`{{ route('leaderboard.loadMore') }}?page=${page}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.html) {
                                        container.insertAdjacentHTML('beforeend', data.html);
                                    }
                                    if (!data.hasMore) {
                                        hasMore = false;
                                    }
                                })
                                .catch(err => console.error(err))
                                .finally(() => {
                                    loading = false;
                                    loadingIndicator.classList.add('hidden');
                                });
                        };

                        window.addEventListener('scroll', () => {
                            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
                                loadMoreLeaderboard();
                            }
                        });
                    });
                </script>

                <!-- Loading Indicator for Leaderboard -->
                    <div id="leaderboard-loading" class="hidden text-center py-4">
                        @svg('heroicon-o-arrow-path', 'w-6 h-6 animate-spin mx-auto text-yellow-500')
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                        <p class="text-gray-400 text-sm">Belum ada data peringkat.</p>
                    </div>
                @endif
                </section>

                <!-- Bagian Merchandise / Produk (Dummy) -->
                <section class="px-4 py-5 bg-gray-50/50">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Merchandise</h3>
                        <span class="text-sm text-blue-600 font-semibold">Lihat Semua</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        @if(isset($products) && count($products) > 0)
                            @foreach($products as $product)
                            <a href="{{ route('product.show', $product->id) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden group block">
                                <div class="relative h-32 bg-gray-200">
                                    <img src="{{ $product->image ? Storage::url($product->image) : 'https://placehold.co/300x300?text=' . urlencode($product->name) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                    <button onclick="event.preventDefault(); addToCart({{ $product->id }})" class="absolute bottom-2 right-2 bg-white p-1.5 rounded-full shadow hover:bg-blue-50 text-blue-600">
                                        @svg('heroicon-o-shopping-cart', 'w-4 h-4')
                                    </button>
                                </div>
                                <div class="p-3">
                                    <h4 class="font-bold text-gray-800 text-sm mb-1 truncate">{{ $product->name }}</h4>
                                    <p class="text-orange-500 font-bold text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                    <div class="flex items-center mt-1">
                                        @svg('heroicon-s-star', 'w-3 h-3 text-yellow-400')
                                        <span class="text-[10px] text-gray-500 ml-1">{{ $product->rating }} ({{ $product->sold_count }} terjual)</span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        @else
                             <div class="col-span-2 text-center py-6 bg-white rounded-xl border border-dashed border-gray-300">
                                <p class="text-gray-400 text-sm">Belum ada produk.</p>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- Spacer to prevent content from being hidden behind bottom nav -->
                <div class="h-40 w-full bg-transparent"></div>


            </div>


