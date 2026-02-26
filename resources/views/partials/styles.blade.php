
    <!-- Tailwind CSS & JS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    @php $fontFamily = $appSettings->font_family ?? 'Poppins'; @endphp
    <link href="https://fonts.googleapis.com/css2?family={{ urlencode($fontFamily) }}:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: '{{ $fontFamily }}', 'Inter', sans-serif;
            background-color: #f3f4f6; /* bg-gray-100 */
            -webkit-tap-highlight-color: transparent;
        }

        /* Custom Scrollbar for Horizontal Scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        .slide-in-right {
            animation: slideInRight 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes scaleUp {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .animate-scale-up {
            animation: scaleUp 0.15s ease-out forwards;
        }
        
        /* Shimmer Effect for Loading (Optional) */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .animate-shimmer {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        /* Grid Layout helpers */
        .layout-list {
            display: flex;
            flex-direction: row;
        }

        /* =============================== */
        /* RESPONSIVE DESKTOP LAYOUT (CSS) */
        /* =============================== */

        /* Mobile: sidebar hidden, bottom nav visible */
        #desktop-sidebar {
            display: none;
        }

        /* Desktop: sidebar visible, bottom nav hidden, content wider */
        @media (min-width: 768px) {
            body {
                background-color: #f8fafc;
            }

            /* App wrapper becomes flex row */
            #app-wrapper {
                display: flex;
                min-height: 100vh;
            }

            /* Show sidebar */
            #desktop-sidebar {
                display: flex !important;
                flex-direction: column;
                width: 16rem; /* w-64 = 256px */
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 40;
                background: #fff;
                border-right: 1px solid #e5e7eb;
                transition: transform 0.3s ease;
            }

            /* Desktop top bar */
            .desktop-topbar {
                display: flex !important;
            }

            /* Content area: offset by sidebar width, no max-w-md */
            #app-container {
                max-width: 100% !important;
                margin-left: 16rem !important; /* match sidebar width */
                background-color: #f8fafc;
                box-shadow: none !important;
            }

            /* Tab content max width for readability */
            #app-container .tab-content {
                max-width: 1200px;
                margin: 0 auto;
            }

            /* Hide bottom nav on desktop */
            #bottom-nav {
                display: none !important;
            }

            /* Reduce bottom padding (no bottom nav) */
            #app-container > .pb-24 {
                padding-bottom: 1.5rem !important;
            }
        }

        /* Larger desktop: wider sidebar */
        @media (min-width: 1024px) {
            #desktop-sidebar {
                width: 18rem; /* lg:w-72 = 288px */
            }
            #app-container {
                margin-left: 18rem !important;
            }
        }
    </style>
    
    <!-- Dynamic Color Theme (Based on Settings) -->
    @if(isset($appSettings) && $appSettings->color_theme)
        @php
            $theme = $appSettings->color_theme;
            $colors = [
                'blue' => '#1e3a8a', // blue-900
                'green' => '#15803d', // green-700
                'red' => '#b91c1c', // red-700
                'orange' => '#c2410c', // orange-700
                'purple' => '#7e22ce', // purple-700
            ];
            $primaryColor = $colors[$theme] ?? $colors['blue'];
        @endphp
        <style>
            :root {
                --primary-color: {{ $primaryColor }};
            }
            .text-primary { color: var(--primary-color); }
            .bg-primary { background-color: var(--primary-color); }
            .border-primary { border-color: var(--primary-color); }
        </style>
    @endif
