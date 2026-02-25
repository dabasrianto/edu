<!-- Post/Article Management Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 flex items-center text-sm">
            @svg('heroicon-s-document-text', 'w-4 h-4 mr-2 text-teal-500')
            Kelola Artikel
        </h3>
        <div class="flex items-center space-x-2">
            <button onclick="togglePostForm()" class="text-[10px] text-white font-bold bg-teal-600 px-2 py-1 rounded hover:bg-teal-700">+ Tambah</button>
            <button onclick="loadPosts()" class="text-[10px] text-teal-600 font-bold bg-teal-50 px-2 py-1 rounded hover:bg-teal-100">Refresh</button>
        </div>
    </div>

    <!-- Add Post Form (Hidden) -->
    <div id="post-form-container" class="hidden p-4 bg-teal-50/30 border-b border-gray-200">
        <form id="add-post-form" class="space-y-3">
            <input type="text" name="title" required placeholder="Judul Artikel" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:border-teal-500 outline-none">
            <div class="grid grid-cols-2 gap-2">
                <select name="category_id" id="post-category-select" required class="text-sm border border-gray-300 rounded-lg p-2 bg-white">
                    <option value="">-- Kategori --</option>
                </select>
                <select name="status" class="text-sm border border-gray-300 rounded-lg p-2 bg-white">
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <textarea name="content" required rows="4" placeholder="Konten artikel..." class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:border-teal-500 outline-none"></textarea>
            <input type="file" name="image" accept="image/*" class="text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700">
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="show_in_slider" id="post-slider" class="rounded border-gray-300 text-teal-600">
                <label for="post-slider" class="text-xs text-gray-700">Tampilkan di Slider</label>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-teal-600 text-white text-xs font-bold py-2 rounded-lg hover:bg-teal-700">Simpan Artikel</button>
                <button type="button" onclick="togglePostForm()" class="bg-gray-200 text-gray-600 text-xs font-bold px-4 py-2 rounded-lg hover:bg-gray-300">Batal</button>
            </div>
        </form>
    </div>

    <!-- Post List -->
    <div id="admin-post-list" class="divide-y divide-gray-50 max-h-96 overflow-y-auto">
        <div class="px-3 py-4 text-center text-gray-400 text-xs">Memuat data...</div>
    </div>
</div>
