
            <!-- ======================= -->
            <!-- TAB: ARTIKEL (BLOG)     -->
            <!-- ======================= -->
            <div id="view-blog" class="tab-content hidden fade-in">
                <div class="sticky top-0 bg-white z-10 px-4 py-3 shadow-sm border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">{{ $appSettings->blog_title ?? 'Artikel Terbaru' }}</h2>
                    
                    <!-- Layout Toggle -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button onclick="setBlogLayout('list')" id="btn-layout-list" class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 transition-colors">
                            @svg('heroicon-o-bars-3', 'w-4 h-4')
                        </button>
                        <button onclick="setBlogLayout('grid-1')" id="btn-layout-grid-1" class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 transition-colors">
                            @svg('heroicon-o-squares-2x2', 'w-4 h-4') <!-- Icon 1 per row (card like) -->
                        </button>
                        <button onclick="setBlogLayout('grid-2')" id="btn-layout-grid-2" class="p-1.5 rounded-md text-gray-400 hover:text-blue-600 transition-colors">
                            @svg('heroicon-o-squares-plus', 'w-4 h-4') <!-- Icon 2 per row -->
                        </button>
                    </div>
                </div>

                <div id="blog-container" class="p-4 grid gap-4 transition-all duration-300 grid-cols-1">
                    @forelse($blogPosts as $post)
                    <article class="blog-item bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col group cursor-pointer" onclick="window.location.href='{{ route('post.show', $post->slug) }}'">
                        <div class="blog-image-container aspect-video w-full overflow-hidden bg-gray-200 relative">
                            @if($post->image)
                                <img src="{{ Storage::url($post->image) }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    @svg('heroicon-o-photo', 'w-10 h-10')
                                </div>
                            @endif
                            <div class="absolute top-2 right-2 bg-blue-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                {{ $post->category->name ?? 'Umum' }}
                            </div>
                        </div>
                        <div class="blog-content-container p-3 flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-900 text-sm mb-1 line-clamp-2 leading-tight group-hover:text-blue-700 transition-colors">{{ $post->title }}</h3>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3 flex-1">{{ Str::limit(strip_tags($post->content), 80) }}</p>
                            <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-50">
                                <span class="text-[10px] text-gray-400 flex items-center">
                                    @svg('heroicon-s-calendar', 'w-3 h-3 mr-1')
                                    {{ $post->created_at->format('d M Y') }}
                                </span>
                                <span class="text-[10px] font-medium text-blue-600">Baca &rarr;</span>
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="col-span-full text-center py-10">
                        @svg('heroicon-o-document-magnifying-glass', 'w-12 h-12 text-gray-300 mx-auto mb-2')
                        <p class="text-gray-400 text-sm">Belum ada artikel.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <script>
                function setBlogLayout(layout) {
                    const container = document.getElementById('blog-container');
                    if (!container) return;
                    
                    const items = container.querySelectorAll('.blog-item');
                    const btnList = document.getElementById('btn-layout-list');
                    const btnGrid1 = document.getElementById('btn-layout-grid-1');
                    const btnGrid2 = document.getElementById('btn-layout-grid-2');

                    // Reset Buttons
                    [btnList, btnGrid1, btnGrid2].forEach(btn => {
                        if(btn) btn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                    });
                    
                    // Highlight Active
                    if (layout === 'list' && btnList) btnList.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                    if (layout === 'grid-1' && btnGrid1) btnGrid1.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                    if (layout === 'grid-2' && btnGrid2) btnGrid2.classList.add('bg-white', 'text-blue-600', 'shadow-sm');

                    // Reset Container Classes
                    container.classList.remove('grid-cols-1', 'grid-cols-2', 'gap-4', 'gap-2');
                    
                    // Reset Items and Apply Layout
                    items.forEach(item => {
                        const img = item.querySelector('.blog-image-container');
                        const content = item.querySelector('.blog-content-container');

                        // 1. Reset everything to Base (Grid) State
                        item.classList.remove('flex-row', 'flex-row-reverse', 'items-center', 'p-2', 'layout-list');
                        item.classList.add('flex-col');
                        
                        if(img) {
                            img.classList.remove('w-24', 'h-24', 'flex-shrink-0', 'ml-3', 'rounded-lg');
                            img.classList.add('aspect-video', 'w-full');
                        }
                        
                        if(content) {
                            content.classList.remove('p-0', 'py-1');
                            content.classList.add('p-3');
                        }

                        // 2. Apply Custom Layout State
                        if (layout === 'list') {
                            // Container overrides
                            item.classList.remove('flex-col');
                            // flex-row-reverse means Image (which is 1st in DOM) goes to Right, Content to Left
                            item.classList.add('flex-row-reverse', 'items-center', 'p-3', 'layout-list'); 

                            // Image overrides
                            if(img) {
                                img.classList.remove('aspect-video', 'w-full');
                                img.classList.add('w-24', 'h-24', 'flex-shrink-0', 'ml-3', 'rounded-lg');
                            }

                            // Content overrides
                            if(content) {
                                content.classList.remove('p-3');
                                content.classList.add('p-0', 'py-1');
                            }
                        }
                    });
                    
                    // Fix Container Grid
                    if (layout === 'grid-1') {
                        container.classList.add('grid-cols-1', 'gap-4');
                    } else if (layout === 'grid-2') {
                        container.classList.add('grid-cols-2', 'gap-4');
                    } else if (layout === 'list') {
                        container.classList.add('grid-cols-1', 'gap-3');
                    }
                    
                    localStorage.setItem('blogLayout', layout);
                }

                // Initial Load
                document.addEventListener('DOMContentLoaded', () => {
                    const savedLayout = localStorage.getItem('blogLayout') || 'list';
                    setBlogLayout(savedLayout);
                });
            </script>
