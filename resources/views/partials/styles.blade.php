
    <!-- Tailwind CSS & JS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
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
