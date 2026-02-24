<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $appSettings->app_name ?? 'Edu HSI' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ ($appSettings->logo_path ?? null) ? Storage::url($appSettings->logo_path) : 'https://ui-avatars.com/api/?name=Edu+HSI&background=1e3a8a&color=fff&size=192' }}">
    <link rel="icon" href="{{ ($appSettings->logo_path ?? null) ? Storage::url($appSettings->logo_path) : 'https://ui-avatars.com/api/?name=Edu+HSI&background=1e3a8a&color=fff&size=32' }}">
    
    @include('partials.styles')
</head>
<body class="bg-gray-100 text-gray-800 antialiased">
    
    @php
        $appSettings = $appSettings ?? \App\Models\AppSetting::first() ?? (object)[];
        $quizzesJson = $quizzesJson ?? [];
        $myAttempts = $myAttempts ?? [];
        // Color Logic
        $activeTextClass = 'text-blue-900';
        if(isset($appSettings->theme_color)) {
             $activeTextClass = match($appSettings->theme_color) {
               'green' => 'text-green-700',
               'red' => 'text-red-700',
               'orange' => 'text-orange-700',
               'purple' => 'text-purple-700',
               default => 'text-blue-900'
           };
        }
    @endphp

    <!-- Pass Data to JS Global Scope -->
    <script>
        window.isAuthenticated = {{ Auth::check() ? 'true' : 'false' }};
        window.quizzesData = @json($quizzesJson);
        window.myAttempts = @json($myAttempts);

        // Global Cart Function
        window.updateCartBadge = () => {
            fetch("{{ route('cart.count') }}")
                .then(res => res.json())
                .then(data => {
                    document.querySelectorAll('.cart-badge').forEach(el => {
                        el.innerText = data.count;
                        if(data.count > 0) el.classList.remove('hidden');
                        else el.classList.add('hidden');
                    });
                })
                .catch(err => console.error(err));
        };
    </script>

    <!-- App Container -->
    <div id="app-container" class="mx-auto w-full max-w-md md:max-w-2xl lg:max-w-3xl bg-white min-h-screen relative shadow-2xl overflow-x-hidden">
        
        <!-- TABLET/DESKTOP HEADER (Hidden on Mobile) -->
        <header class="hidden md:flex justify-between items-center p-4 bg-white shadow-sm sticky top-0 z-40 border-b border-gray-100">
            <div class="flex items-center space-x-2">
                 <img src="{{ ($appSettings->logo_path ?? null) ? Storage::url($appSettings->logo_path) : 'https://ui-avatars.com/api/?name=Edu+HSI&background=1e3a8a&color=fff&size=40' }}" class="w-10 h-10 rounded-lg" alt="Logo">
                 <div>
                     <h1 class="font-bold text-lg text-blue-900">{{ $appSettings->app_name ?? 'Edu HSI' }}</h1>
                     <p class="text-[10px] text-gray-500">Belajar Di Mana Saja</p>
                 </div>
            </div>
            <div class="flex items-center space-x-4">
                 @auth
                    <button onclick="toggleModal('topup-modal')" class="bg-blue-900 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-800 transition-colors flex items-center shadow-md active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1">
                          <path fill-rule="evenodd" d="M12 3.75a.75.75 0 01.75.75v6.75h6.75a.75.75 0 010 1.5h-6.75v6.75a.75.75 0 01-1.5 0v-6.75H4.5a.75.75 0 010-1.5h6.75V4.5a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                        </svg>
                        Top Up
                    </button>
                 @endauth
                 <a href="/?tab=cart" onclick="event.preventDefault(); switchTab('cart');" class="relative p-2 text-gray-500 hover:text-blue-900 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                      <path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 004.25 22.5h15.5a1.875 1.875 0 001.865-2.071l-1.263-12a1.875 1.875 0 00-1.865-1.679H16.5V6a4.5 4.5 0 10-9 0zM12 3a3 3 0 00-3 3v.75h6V6a3 3 0 00-3-3zm-3 8.25a3 3 0 106 0v-.75a.75.75 0 011.5 0v.75a4.5 4.5 0 11-9 0v-.75a.75.75 0 011.5 0v.75z" clip-rule="evenodd" />
                    </svg>
                    <span class="cart-badge absolute top-1 right-0 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
                 </a>
            </div>
        </header>

        <!-- Main Content with Padding for Bottom Nav -->
        <div class="pb-24">
            @hasSection('content')
                @yield('content')
                <!-- MODALS (Global) -->
                @include('partials.modals.payment')
                @include('partials.modals.proof')
                @include('partials.modals.auth-check')
                @include('partials.modals.quiz')
                @include('partials.modals.result')
                @include('partials.modals.topup')
            @else
                <!-- TABS -->
                @include('partials.tabs.home')
                @include('partials.tabs.blog')
                @include('partials.tabs.academy')
                @include('partials.tabs.regular')
                @include('partials.tabs.course-detail')
                @include('partials.tabs.login')
                @include('partials.tabs.profile')
                @include('partials.tabs.cart')
                @include('partials.tabs.admin')

                <!-- MODALS -->
                @include('partials.modals.payment')
                @include('partials.modals.proof')
                @include('partials.modals.auth-check')
                @include('partials.modals.quiz')
                @include('partials.modals.result')
                @include('partials.modals.topup')
            @endif
        </div>

        <!-- BOTTOM NAV (Always Visible) -->
        <!-- Moved outside app-container to prevent clipping/overflow issues -->
    </div>

    @include('partials.bottom-nav')

    @if($aiChatEnabled ?? false)
        @include('partials.ai-chat')
    @endif
    
    <!-- SCRIPTS -->
    @include('partials.scripts')

    <script>
        function toggleAiChat() {
            const el = document.getElementById('ai-chat-window');
            if(el) el.classList.toggle('hidden');
        }
    </script>

</body>
</html>