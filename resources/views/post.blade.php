<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $banner->title }} - HSI Edu</title>
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
            <h1 class="font-bold text-lg text-blue-900 truncate max-w-[200px]">{{ $banner->title }}</h1>
            <div class="w-6"></div> <!-- Spacer for center alignment -->
        </div>
    </header>

    <main class="max-w-md mx-auto">
        <!-- Hero Image -->
        @if($banner->image)
        <div class="w-full aspect-video relative">
            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
            @if($banner->subtitle)
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                <p class="text-white text-sm font-medium">{{ $banner->subtitle }}</p>
            </div>
            @endif
        </div>
        @endif

        <!-- Content -->
        <article class="p-5 bg-white min-h-[500px]">
             <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $banner->title }}</h1>
             <div class="flex items-center text-xs text-gray-500 mb-6 space-x-2">
                 <span>Date: {{ $banner->created_at->format('d M Y') }}</span>
                 <span>&bull;</span>
                 <span>Admin</span>
             </div>

             <div class="prose prose-blue prose-sm max-w-none text-gray-700 leading-relaxed">
                 {!! nl2br(e($banner->content)) !!}
             </div>
        </article>
    </main>

    <!-- Floating Action Button (Optional, for easy access) -->
    <!-- <div class="fixed bottom-6 right-6 max-w-md mx-auto w-full px-6 flex justify-end pointer-events-none">
        <a href="/" class="bg-blue-600 text-white p-3 rounded-full shadow-lg pointer-events-auto hover:bg-blue-700 transition-colors">
            @svg('heroicon-o-home', 'w-6 h-6')
        </a>
    </div> -->

</body>
</html>
