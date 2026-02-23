<a href="{{ route('post.show', $post->slug) }}" class="flex md:flex-col bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
    @if($post->image)
    <img src="{{ Storage::url($post->image) }}" class="w-24 h-24 md:w-full md:h-40 rounded-lg object-cover flex-shrink-0 bg-gray-100">
    @else
    <div class="w-24 h-24 md:w-full md:h-40 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 text-gray-400">
        @svg('heroicon-o-photo', 'w-8 h-8')
    </div>
    @endif
    <div class="ml-3 md:ml-0 md:mt-3 flex flex-col justify-center w-full">
        <div class="flex justify-between items-start">
            <span class="text-[10px] font-bold text-{{ $post->category->color ?? 'blue' }}-600 mb-1 uppercase tracking-wide bg-gray-50 px-2 py-0.5 rounded-full">{{ $post->category->name ?? 'Umum' }}</span>
        </div>
        <h4 class="font-bold text-gray-900 text-sm md:text-base line-clamp-2 leading-tight mb-1">{{ $post->title }}</h4>
        <div class="flex items-center text-xs md:text-sm text-gray-400 mt-1">
            @svg('heroicon-m-calendar', 'w-3 h-3 mr-1')
            {{ $post->created_at->diffForHumans() }}
        </div>
    </div>
</a>
