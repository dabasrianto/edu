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

    <!-- App Container (Desktop: flex with sidebar, Mobile: single column) -->
    <div id="app-wrapper" class="md:flex md:min-h-screen">

        <!-- ========== DESKTOP SIDEBAR (Hidden on Mobile) ========== -->
        <aside id="desktop-sidebar" class="hidden md:flex flex-col w-64 lg:w-72 bg-white border-r border-gray-200 fixed top-0 left-0 bottom-0 z-40">
            <!-- Sidebar Header / Logo -->
            <div class="p-5 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <img src="{{ ($appSettings->logo_path ?? null) ? Storage::url($appSettings->logo_path) : 'https://ui-avatars.com/api/?name=Edu+HSI&background=1e3a8a&color=fff&size=40' }}" class="w-10 h-10 rounded-lg shadow-sm" alt="Logo">
                    <div>
                        <h1 class="font-bold text-base text-blue-900">{{ $appSettings->app_name ?? 'Edu HSI' }}</h1>
                        <p class="text-[10px] text-gray-400">Belajar Di Mana Saja</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1" id="sidebar-nav">
                @php
                    $defaultMenu = [
                        ['id' => 'home', 'label' => 'Beranda', 'icon' => 'heroicon-o-home', 'icon_solid' => 'heroicon-s-home'],
                        ['id' => 'blog', 'label' => 'Artikel', 'icon' => 'heroicon-o-newspaper', 'icon_solid' => 'heroicon-s-newspaper'],
                        ['id' => 'akademi', 'label' => 'Akademi', 'icon' => 'heroicon-o-academic-cap', 'icon_solid' => 'heroicon-s-academic-cap'],
                        ['id' => 'reguler', 'label' => 'Reguler', 'icon' => 'heroicon-o-book-open', 'icon_solid' => 'heroicon-s-book-open'],
                        ['id' => 'cart', 'label' => 'Keranjang', 'icon' => 'heroicon-o-shopping-bag', 'icon_solid' => 'heroicon-s-shopping-bag'],
                        ['id' => 'profil', 'label' => 'Profil', 'icon' => 'heroicon-o-user', 'icon_solid' => 'heroicon-s-user'],
                    ];
                @endphp

                @foreach($defaultMenu as $sideMenu)
                <button onclick="switchTab('{{ $sideMenu['id'] }}')" id="sidebar-nav-{{ $sideMenu['id'] }}"
                    class="sidebar-nav-item w-full flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ $sideMenu['id'] == 'home' ? 'bg-blue-50 text-blue-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <span class="sidebar-icon-inactive {{ $sideMenu['id'] == 'home' ? 'hidden' : '' }}">
                        @svg($sideMenu['icon'], 'w-5 h-5')
                    </span>
                    <span class="sidebar-icon-active {{ $sideMenu['id'] == 'home' ? '' : 'hidden' }}">
                        @svg($sideMenu['icon_solid'], 'w-5 h-5')
                    </span>
                    <span>{{ $sideMenu['label'] }}</span>
                    @if($sideMenu['id'] == 'cart')
                        <span class="cart-badge ml-auto bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center hidden">0</span>
                    @endif
                </button>
                @endforeach

                @if(Auth::check() && Auth::user()->is_admin)
                <div class="pt-3 mt-3 border-t border-gray-100">
                    <button onclick="switchTab('admin')" id="sidebar-nav-admin"
                        class="sidebar-nav-item w-full flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900 transition-all duration-200">
                        <span class="sidebar-icon-inactive">@svg('heroicon-o-cog-6-tooth', 'w-5 h-5')</span>
                        <span class="sidebar-icon-active hidden">@svg('heroicon-s-cog-6-tooth', 'w-5 h-5')</span>
                        <span>Admin</span>
                    </button>
                </div>
                @endif
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-gray-100">
                @auth
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs overflow-hidden flex-shrink-0">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ substr(Auth::user()->name, 0, 2) }}
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-400 truncate">Saldo: Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <button onclick="toggleModal('topup-modal')" class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors flex-shrink-0" title="Top Up">
                        @svg('heroicon-s-plus', 'w-4 h-4')
                    </button>
                </div>
                @else
                <button onclick="switchTab('login')" class="w-full bg-blue-900 text-white text-sm font-bold py-2.5 rounded-xl hover:bg-blue-800 transition-colors">
                    Login
                </button>
                @endauth
            </div>
        </aside>

        <!-- ========== MAIN CONTENT AREA ========== -->
        <div id="app-container" class="mx-auto w-full max-w-md md:max-w-full md:ml-64 lg:ml-72 bg-white min-h-screen relative shadow-2xl md:shadow-none overflow-x-hidden">
        
            <!-- DESKTOP TOP BAR (Hidden on Mobile) -->
            <header class="desktop-topbar hidden md:flex justify-between items-center px-6 py-3 bg-white shadow-sm sticky top-0 z-30 border-b border-gray-100">
                <div>
                    <h2 class="font-bold text-gray-800 text-lg" id="desktop-page-title">Beranda</h2>
                </div>
                <div class="flex items-center space-x-3">
                     @auth
                        <button onclick="toggleModal('topup-modal')" class="bg-blue-900 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-800 transition-colors flex items-center shadow-md active:scale-95">
                            @svg('heroicon-s-plus', 'w-4 h-4 mr-1')
                            Top Up
                        </button>
                     @endauth
                     <a href="/?tab=cart" onclick="event.preventDefault(); switchTab('cart');" class="relative p-2 text-gray-500 hover:text-blue-900 transition-colors rounded-lg hover:bg-gray-50">
                        @svg('heroicon-o-shopping-bag', 'w-5 h-5')
                        <span class="cart-badge absolute top-0.5 right-0.5 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
                     </a>
                </div>
            </header>

            <!-- Main Content with Padding for Bottom Nav -->
            <div class="pb-24 md:pb-6">
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


                    <!-- MODALS -->
                    @include('partials.modals.payment')
                    @include('partials.modals.proof')
                    @include('partials.modals.auth-check')
                    @include('partials.modals.quiz')
                    @include('partials.modals.result')
                    @include('partials.modals.topup')
                @endif
            </div>

            <!-- BOTTOM NAV (Always Visible on Mobile) -->
        </div>
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