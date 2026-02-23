<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} - HSI Edu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen pb-20">

    <!-- Header / Navbar -->
    <header class="bg-white sticky top-0 z-50 shadow-sm border-b border-gray-100">
        <div class="max-w-md mx-auto px-4 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center text-gray-600 hover:text-blue-900 transition-colors">
                @svg('heroicon-o-arrow-left', 'w-6 h-6 mr-2')
                <span class="font-bold text-sm">Kembali</span>
            </a>
            <h1 class="font-bold text-lg text-blue-900 truncate max-w-[200px]">{{ $post->title }}</h1>
            <div class="w-6"></div> <!-- Spacer for center alignment -->
        </div>
    </header>

    <main class="max-w-md mx-auto">
        <!-- Hero Image -->
        @if($post->image)
        <div class="w-full aspect-video relative">
            <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            @if($post->category)
            <div class="absolute bottom-4 left-4">
                 <span class="bg-{{ $post->category->color ?? 'blue' }}-600 text-white text-xs px-2 py-1 rounded-full shadow-sm">
                    {{ $post->category->name }}
                 </span>
            </div>
            @endif
        </div>
        @endif

        <!-- Content -->
        <article class="p-5 bg-white min-h-[500px]">
             <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $post->title }}</h1>
             <div class="flex items-center text-xs text-gray-500 mb-6 space-x-2">
                 <span>{{ $post->created_at->format('d M Y') }}</span>
                 <span>&bull;</span>
                 <span>{{ $post->author?->name ?? 'Admin' }}</span>
             </div>

             <div class="prose prose-blue prose-sm max-w-none text-gray-700 leading-relaxed">
                 {!! $post->content !!}
             </div>
        </article>
    </main>

</body>
</html>
