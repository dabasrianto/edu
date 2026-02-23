
            <!-- ======================= -->
            <!-- TAB: ADMIN (SETTINGS)  -->
            <!-- ======================= -->
            <div id="view-admin" class="tab-content hidden fade-in pb-20">
                @if(Auth::check() && Auth::user()->is_admin)
                <div class="bg-white sticky top-0 z-10 border-b border-gray-100 shadow-sm px-4 py-3 flex justify-between items-center">
                    <h2 class="font-bold text-gray-900 text-lg">Halaman Admin</h2>
                    <a href="/admin" class="text-xs bg-gray-100 px-3 py-1 rounded-lg text-gray-600 font-bold hover:bg-gray-200">
                        Ke Panel Utama &rarr;
                    </a>
                </div>

                <div class="p-6 space-y-8">
                    
                    <!-- Section A: Manajemen Pendaftaran (Enrollment) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 flex items-center text-sm">
                                @svg('heroicon-s-clipboard-document-list', 'w-4 h-4 mr-2 text-blue-600')
                                Konfirmasi Pendaftaran
                            </h3>
                            <button onclick="fetchEnrollments()" class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded hover:bg-blue-100 transition-colors">
                                Refresh
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase">User</th>
                                        <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase">Program</th>
                                        <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase">Bukti</th>
                                        <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase text-center">Status</th>
                                        <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-enrollment-list" class="divide-y divide-gray-50 text-xs">
                                     <tr>
                                         <td colspan="5" class="px-3 py-4 text-center text-gray-400">Memuat data...</td>
                                     </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <!-- Section B: Pengaturan Toko -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                             <h3 class="font-bold text-gray-800 flex items-center text-sm">
                                @svg('heroicon-s-cog-6-tooth', 'w-4 h-4 mr-2 text-gray-500')
                                Pengaturan Umum
                            </h3>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf
                                
                                <!-- Logo & Identity -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-2">Logo Aplikasi</label>
                                        <div class="flex items-center space-x-4">
                                            @if($appSettings->logo_path)
                                                <img src="{{ Storage::url($appSettings->logo_path) }}" class="h-12 w-auto border rounded p-1">
                                            @endif
                                            <input type="file" name="logo" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        </div>
                                    </div>
                                    <div>
                                         <label class="block text-xs font-bold text-gray-700 mb-2">Favicon</label>
                                         <div class="flex items-center space-x-4">
                                            @if($appSettings->favicon_path)
                                                <img src="{{ Storage::url($appSettings->favicon_path) }}" class="h-8 w-8 border rounded p-1">
                                            @endif
                                            <input type="file" name="favicon" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Font Family</label>
                                        <select name="font_family" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:border-blue-500 outline-none bg-white">
                                            <option value="Inter, sans-serif" {{ $appSettings->font_family == 'Inter, sans-serif' ? 'selected' : '' }}>Inter (Modern)</option>
                                            <option value="Roboto, sans-serif" {{ $appSettings->font_family == 'Roboto, sans-serif' ? 'selected' : '' }}>Roboto (Android Default)</option>
                                            <option value="Open Sans, sans-serif" {{ $appSettings->font_family == 'Open Sans, sans-serif' ? 'selected' : '' }}>Open Sans</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 mb-1">Theme Color</label>
                                        <select name="theme_color" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:border-blue-500 outline-none bg-white">
                                            <option value="blue" {{ $appSettings->theme_color == 'blue' ? 'selected' : '' }}>Blue (Default)</option>
                                            <option value="emerald" {{ $appSettings->theme_color == 'emerald' ? 'selected' : '' }}>Emerald Green</option>
                                            <option value="purple" {{ $appSettings->theme_color == 'purple' ? 'selected' : '' }}>Royal Purple</option>
                                            <option value="gray" {{ $appSettings->theme_color == 'gray' ? 'selected' : '' }}>Neutral Gray</option>
                                            <option value="red" {{ $appSettings->theme_color == 'red' ? 'selected' : '' }}>Red Alert</option>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                <!-- Home Config Section -->
                                <div>
                                    <h4 class="font-bold text-gray-800 text-xs mb-3 uppercase tracking-wider">Halaman Beranda</h4>
                                    <div class="space-y-3">
                                        <input type="text" name="home_greeting" value="{{ $appSettings->home_config['greeting'] ?? '' }}" placeholder="Greeting Text (e.g. Assalamualaikum)" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                                        
                                         <div class="p-3 bg-gray-50 rounded text-xs text-gray-500">
                                             Pengaturan Info Pendaftaran & Banner Slider dikelola melalui menu terpisah di panel ini.
                                         </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- WordPress Sync Section -->
                                <div class="space-y-4 bg-green-50/30 p-4 rounded-xl border border-green-100">
                                     <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider flex items-center">
                                         @svg('heroicon-s-arrow-path', 'w-4 h-4 mr-2 text-green-600')
                                         Sinkronisasi Blog WordPress
                                     </h4>
                                     
                                     <div class="flex items-center space-x-2">
                                         <input type="checkbox" name="wp_sync_enabled" id="wp_sync_enabled" value="1" {{ ($appSettings->blog_config['wp_sync_enabled'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                         <label for="wp_sync_enabled" class="text-xs font-bold text-gray-700">Aktifkan Sinkronisasi Otomatis</label>
                                     </div>

                                     <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                         <div>
                                             <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">URL Situs WordPress</label>
                                             <input type="url" name="wp_sync_url" value="{{ $appSettings->blog_config['wp_sync_url'] ?? '' }}" placeholder="https://namasitus.com" class="w-full text-sm border-gray-300 rounded-lg p-2 border">
                                         </div>
                                         <div>
                                             <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Kategori Tujuan</label>
                                             <select name="wp_sync_category_id" class="w-full text-sm border-gray-300 rounded-lg p-2 border bg-white">
                                                 <option value="">-- Pilih Kategori --</option>
                                                 @foreach(\App\Models\Category::all() as $category)
                                                     <option value="{{ $category->id }}" {{ ($appSettings->blog_config['wp_sync_category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                                         {{ $category->name }}
                                                     </option>
                                                 @endforeach
                                             </select>
                                         </div>
                                     </div>

                                     <div>
                                         <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase">Batas Artikel</label>
                                         <input type="number" name="wp_sync_limit" value="{{ $appSettings->blog_config['wp_sync_limit'] ?? 10 }}" min="1" max="100" class="w-24 text-sm border-gray-300 rounded-lg p-2 border">
                                     </div>
                                </div>
                                
                                <button type="submit" class="w-full bg-blue-900 text-white font-bold py-3 rounded-xl hover:bg-blue-800 transition-colors shadow-md">
                                    Simpan Pengaturan
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Section C: Banner Slider Manager -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                         <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                             <h3 class="font-bold text-gray-800 flex items-center text-sm">
                                @svg('heroicon-s-photo', 'w-4 h-4 mr-2 text-blue-500')
                                Kelola Banner Slider
                            </h3>
                            <span class="text-[10px] text-gray-500">{{ count($banners ?? []) }} Banner Aktif</span>
                        </div>
                        
                        <!-- List Existing Banners -->
                        @if(count($banners ?? []) > 0)
                        <div class="max-h-60 overflow-y-auto divide-y divide-gray-100">
                            @foreach($banners as $banner)
                            <div class="p-3 flex items-center space-x-3 hover:bg-gray-50 transition-colors">
                                <img src="{{ $banner->image_url }}" class="w-16 h-8 object-cover rounded shadow-sm border border-gray-200">
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-800 line-clamp-1">{{ $banner->title }}</p>
                                    <p class="text-[10px] text-gray-500">Urutan: {{ $banner->order }}</p>
                                </div>
                                <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('Hapus banner ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 p-1.5 rounded-md transition-colors" title="Hapus">
                                        @svg('heroicon-o-trash', 'w-4 h-4')
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="p-4 text-center text-gray-400 text-xs italic">Belum ada banner.</div>
                        @endif

                        <!-- Add New Banner Form -->
                        <div class="p-4 bg-gray-50 border-t border-gray-100">
                            <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <input type="hidden" name="type" value="banner">
                                <input type="hidden" name="status" value="published">
                                
                                <div class="grid grid-cols-12 gap-2">
                                    <div class="col-span-8">
                                        <input type="text" name="title" required placeholder="Judul Banner" class="w-full text-xs border border-gray-300 rounded p-2 focus:border-blue-500 outline-none">
                                    </div>
                                    <div class="col-span-4">
                                        <input type="number" name="order" value="1" placeholder="Urutan" class="w-full text-xs border border-gray-300 rounded p-2 focus:border-blue-500 outline-none">
                                    </div>
                                </div>
                                <input type="text" name="subtitle" placeholder="Sub-judul (Opsional)" class="w-full text-xs border border-gray-300 rounded p-2 focus:border-blue-500 outline-none">
                                <input type="file" name="image" required class="w-full text-xs bg-white border border-gray-300 rounded p-1.5">
                                
                                <button type="submit" class="w-full bg-blue-600 text-white text-xs font-bold py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    + Tambah Banner
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Section D: Kelola Kursus (Course Management) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 flex items-center text-sm">
                                @svg('heroicon-s-academic-cap', 'w-4 h-4 mr-2 text-indigo-500')
                                Kelola Kursus
                            </h3>
                        </div>

                        <!-- List Courses -->
                        <div class="p-4 border-b border-gray-100 bg-gray-50">
                            <h4 class="text-xs font-bold text-gray-700 mb-3 uppercase tracking-wide">Daftar Kursus Aktif</h4>
                            
                            <div class="space-y-3">
                                @if(isset($allCourses) && count($allCourses) > 0)
                                    @foreach($allCourses as $course)
                                    <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                                        
                                        <!-- Header Item -->
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-2 h-8 rounded-full bg-{{ $course->color }}-500 block"></span>
                                                <div>
                                                    <h5 class="font-bold text-sm text-gray-900 leading-tight">{{ $course->title }}</h5>
                                                    <span class="text-[10px] text-gray-500 uppercase font-semibold">{{ $course->type }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <!-- Delete Button -->
                                                <form action="/admin/courses/{{ $course->id }}" method="POST" onsubmit="return confirm('Hapus kursus ini beserta materi?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1 text-gray-400 hover:text-red-500 transition-colors">
                                                        @svg('heroicon-o-trash', 'w-4 h-4')
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <!-- Footer Item Info -->
                                        <div class="flex justify-between items-center text-xs text-gray-500 border-t border-gray-100 pt-2 mt-2">
                                            <span>Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                            <span>{{ $course->materials->count() }} Materi</span>
                                        </div>

                                        <!-- Simple Material Adder Inline -->
                                        <div class="mt-3 bg-gray-50 p-2 rounded border border-dashed border-gray-200">
                                            <p class="text-[10px] font-bold text-gray-400 mb-2">Kelola Materi ({{ $course->materials->count() }})</p>
                                    
                                            <!-- List Existing Materials -->
                                            <ul class="space-y-2 mb-3">
                                                @foreach($course->materials as $material)
                                                <li class="flex justify-between items-center bg-gray-50 p-2 rounded text-xs">
                                                    <div class="flex items-center space-x-2">
                                                         <span class="px-1.5 py-0.5 rounded bg-white border text-[10px] text-gray-500">{{ $material->type }}</span>
                                                         <span class="font-medium text-gray-700">{{ $material->title }}</span>
                                                    </div>
                                                    <form action="/admin/materials/{{ $material->id }}" method="POST" onsubmit="return confirm('Hapus materi ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold px-1">
                                                            @svg('heroicon-o-trash', 'w-4 h-4')
                                                        </button>
                                                    </form>
                                                </li>
                                                @endforeach
                                            </ul>
                                            
                                            <!-- Add Material Form -->
                                            <form action="/admin/materials" method="POST" class="space-y-2">
                                                @csrf
                                                <input type="hidden" name="course_id" value="{{ $course->id }}">
                                                
                                                <!-- Row 1: Basic Info -->
                                                <div class="grid grid-cols-12 gap-2">
                                                    <div class="col-span-12 md:col-span-6">
                                                        <input type="text" name="title" required placeholder="Judul Materi" class="w-full text-xs border border-gray-300 rounded p-1.5 focus:border-blue-500 outline-none">
                                                    </div>
                                                    <div class="col-span-6 md:col-span-3">
                                                        <select name="type" class="w-full text-xs border border-gray-300 rounded p-1.5 focus:border-blue-500 outline-none">
                                                            <option value="video">Video</option>
                                                            <option value="text">Artikel</option>
                                                            <option value="quiz">Kuis</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-span-6 md:col-span-3">
                                                        <input type="text" name="duration" placeholder="Durasi (Text)" class="w-full text-xs border border-gray-300 rounded p-1.5 focus:border-blue-500 outline-none">
                                                    </div>
                                                </div>

                                                <!-- Row 2: Media & Config -->
                                                <div class="grid grid-cols-12 gap-2 items-center">
                                                    <div class="col-span-12 md:col-span-5">
                                                        <input type="url" name="media_url" placeholder="URL Video/Audio (Embed)" class="w-full text-xs border border-gray-300 rounded p-1.5 focus:border-blue-500 outline-none">
                                                    </div>
                                                    <div class="col-span-6 md:col-span-3">
                                                        <input type="number" name="timer_seconds" placeholder="Timer (Detik)" min="0" class="w-full text-xs border border-gray-300 rounded p-1.5 focus:border-blue-500 outline-none">
                                                    </div>
                                                    <div class="col-span-6 md:col-span-4">
                                                        <button type="submit" class="w-full bg-blue-100 text-blue-700 text-xs font-bold p-1.5 rounded hover:bg-blue-200 transition-colors">
                                                            + Tambah Materi
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-center text-gray-400 text-sm py-4">Belum ada data kursus.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Add New Course Form -->
                        <div class="p-4 border-t border-gray-200">
                             <h4 class="text-xs font-bold text-gray-700 mb-3 uppercase tracking-wide">Tambah Kursus Baru</h4>
                             <form action="/admin/courses" method="POST" class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-3">
                                @csrf
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1">Judul Kursus</label>
                                        <input type="text" name="title" required class="w-full text-xs border border-gray-300 rounded p-2 focus:border-blue-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1">Tipe</label>
                                         <select name="type" class="w-full text-xs border border-gray-300 rounded p-2 focus:border-blue-500 outline-none bg-white">
                                            <option value="Reguler">Reguler</option>
                                            <option value="Intensif">Intensif</option>
                                            <option value="Bootcamp">Bootcamp</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 mb-1">Deskripsi Singkat</label>
                                    <textarea name="short_desc" rows="2" class="w-full text-xs border border-gray-300 rounded p-2 focus:border-blue-500 outline-none"></textarea>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1">Harga (IDR)</label>
                                        <input type="number" name="price" value="0" class="w-full text-xs border border-gray-300 rounded p-2 focus:border-blue-500 outline-none">
                                        <span class="text-[9px] text-gray-400">0 = Gratis</span>
                                    </div>
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1">Warna Tema</label>
                                        <select name="color" class="w-full text-xs border border-gray-300 rounded p-2 focus:border-blue-500 outline-none bg-white">
                                            <option value="blue">Blue</option>
                                            <option value="green">Green</option>
                                            <option value="orange">Orange</option>
                                            <option value="purple">Purple</option>
                                            <option value="red">Red</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-1 flex items-end">
                                        <button type="submit" class="w-full bg-indigo-600 text-white text-xs font-bold py-2 rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                                            Simpan Kursus
                                        </button>
                                    </div>
                                </div>
                             </form>
                        </div>
                    </div>
                    
                    <!-- Flash Message -->
                    @if(session('success'))
                        <div class="bg-green-50 text-green-700 p-3 rounded-lg text-sm mt-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- More features can be added here later -->
                    <div class="p-8 text-center text-gray-400 text-sm italic">
                        Fitur admin lainnya akan segera hadir di sini...
                    </div>
                </div>
                @else
                    <div class="flex flex-col items-center justify-center p-10 h-full text-center">
                         @svg('heroicon-s-lock-closed', 'w-12 h-12 text-gray-300 mb-4')
                         <h3 class="text-lg font-bold text-gray-900">Akses Ditolak</h3>
                         <p class="text-sm text-gray-500 mt-2">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
                         <button onclick="switchTab('home')" class="mt-6 bg-blue-900 text-white px-6 py-2 rounded-lg font-bold text-sm">Kembali ke Beranda</button>
                    </div>
                @endif
            </div>
