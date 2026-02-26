
        <nav id="bottom-nav"
    class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex justify-around items-center p-2 z-40 transition-transform duration-300 mx-auto w-full max-w-md md:hidden">
            
            @php
                // Default Menu Config (Using Blade Heroicons names)
                // Prefix: heroicon-o- (Outline) for default. Code will swap to heroicon-s- (Solid) for active.
                $defaultMenu = [
                    ['id' => 'home', 'label' => 'Beranda', 'icon' => 'heroicon-o-home'],
                    ['id' => 'akademi', 'label' => 'Akademi', 'icon' => 'heroicon-o-academic-cap'],
                    ['id' => 'reguler', 'label' => 'Reguler', 'icon' => 'heroicon-o-book-open'],
                    ['id' => 'profil', 'label' => 'Profil', 'icon' => 'heroicon-o-user']
                ];
                $menuItems = $appSettings->menu_config ?? $defaultMenu;
            @endphp

            @foreach($menuItems as $menu)
            <button onclick="switchTab('{{ $menu['id'] }}')" id="nav-{{ $menu['id'] }}"
                class="nav-item flex flex-col items-center {{ $menu['id'] == 'home' ? 'text-blue-900 ' . $activeTextClass : 'text-gray-400' }} hover:text-blue-600 w-full transition-colors relative group">
                
                <div class="icon-container w-6 h-6 relative flex justify-center items-center">
                    @php
                        $icon = $menu['icon'];
                        
                        // Smart Fallback Logic if icon is null/empty
                        if (empty($icon)) {
                            $labelLower = strtolower($menu['label'] ?? '');
                            $idLower = strtolower($menu['id'] ?? '');
                            
                            if (str_contains($labelLower, 'beranda') || str_contains($labelLower, 'home') || str_contains($idLower, 'home')) {
                                $icon = 'heroicon-o-home';
                            } elseif (str_contains($labelLower, 'akademi') || str_contains($labelLower, 'kursus') || str_contains($labelLower, 'belajar') || str_contains($idLower, 'akademi')) {
                                $icon = 'heroicon-o-academic-cap';
                            } elseif (str_contains($labelLower, 'reguler') || str_contains($labelLower, 'program') || str_contains($idLower, 'reguler')) {
                                $icon = 'heroicon-o-book-open';
                            } elseif (str_contains($labelLower, 'profil') || str_contains($labelLower, 'akun') || str_contains($labelLower, 'user') || str_contains($idLower, 'profil')) {
                                $icon = 'heroicon-o-user';
                            } elseif (str_contains($labelLower, 'admin') || str_contains($idLower, 'admin')) {
                                $icon = 'heroicon-o-cog-6-tooth'; // or lock-closed
                            } else {
                                $icon = 'heroicon-o-square-2-stack'; // Generic default
                            }
                        }
                    @endphp
                    @if(str_starts_with($icon, '<svg'))
                        {{-- Backward compatibility for raw SVG --}}
                        <div class="icon-inactive">{!! $icon !!}</div>
                        <div class="icon-active hidden">{!! $icon !!}</div>
                    @elseif(str_starts_with($icon, 'heroicon-'))
                        {{-- Blade Icon Component --}}
                        {{-- Inactive (Outline) --}}
                        <div class="icon-inactive">
                            @svg($icon, 'w-6 h-6')
                        </div>
                        {{-- Active (Solid) - Try to swap -o- to -s- --}}
                        <div class="icon-active hidden">
                            @php
                                $solidIcon = str_replace('-o-', '-s-', $icon);
                                if($solidIcon === $icon) $solidIcon = $icon; 
                            @endphp
                            @svg($solidIcon, 'w-6 h-6')
                        </div>
                    @else
                        {{-- Fallback for invalid icon names (Prevent Crash) --}}
                        <div class="icon-inactive text-gray-400">
                             @svg('heroicon-o-question-mark-circle', 'w-6 h-6')
                        </div>
                        <div class="icon-active hidden text-blue-600">
                             @svg('heroicon-s-question-mark-circle', 'w-6 h-6')
                        </div>
                    @endif
                </div>
                
                <span class="text-xs font-medium mt-1">{{ $menu['label'] }}</span>
            </button>
            @endforeach

            @if(Auth::check() && Auth::user()->is_admin)
            <!-- Nav Item: Admin (Fixed) -->
            <button onclick="switchTab('admin')" id="nav-admin"
                class="nav-item flex flex-col items-center text-gray-400 hover:text-red-600 w-full transition-colors">
                @svg('heroicon-s-cog-6-tooth', 'w-6 h-6 icon-active hidden')
                @svg('heroicon-o-cog-6-tooth', 'w-6 h-6 icon-inactive')
                <span class="text-xs font-medium mt-1">Admin</span>
            </button>
            @endif
        </nav>
