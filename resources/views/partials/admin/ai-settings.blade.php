<!-- AI Settings Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 flex items-center text-sm">
            @svg('heroicon-s-cpu-chip', 'w-4 h-4 mr-2 text-pink-500')
            Pengaturan AI Chatbot
        </h3>
        <div class="flex items-center space-x-2">
            <button onclick="toggleAiForm()" class="text-[10px] text-white font-bold bg-pink-600 px-2 py-1 rounded hover:bg-pink-700">+ Tambah</button>
            <button onclick="loadAiSettings()" class="text-[10px] text-pink-600 font-bold bg-pink-50 px-2 py-1 rounded hover:bg-pink-100">Refresh</button>
        </div>
    </div>
    <!-- Add AI Setting Form -->
    <div id="ai-form-container" class="hidden p-4 bg-pink-50/30 border-b border-gray-200">
        <form id="add-ai-form" class="space-y-3">
            <div class="grid grid-cols-2 gap-2">
                <select name="provider" required class="text-sm border border-gray-300 rounded-lg p-2 bg-white">
                    <option value="gemini">Google Gemini</option>
                    <option value="openai">OpenAI</option>
                    <option value="groq">Groq</option>
                    <option value="qwen">Qwen</option>
                </select>
                <input type="text" name="selected_model" required placeholder="Model (e.g. gemini-pro)" class="text-sm border border-gray-300 rounded-lg p-2">
            </div>
            <input type="text" name="api_key" required placeholder="API Key" class="w-full text-sm border border-gray-300 rounded-lg p-2">
            <textarea name="system_prompt" rows="2" placeholder="System Prompt (opsional)" class="w-full text-sm border border-gray-300 rounded-lg p-2"></textarea>
            <input type="url" name="reference_url" placeholder="Reference URL (opsional)" class="w-full text-sm border border-gray-300 rounded-lg p-2">
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-pink-600 text-white text-xs font-bold py-2 rounded-lg hover:bg-pink-700">Simpan</button>
                <button type="button" onclick="toggleAiForm()" class="bg-gray-200 text-gray-600 text-xs font-bold px-4 py-2 rounded-lg">Batal</button>
            </div>
        </form>
    </div>
    <div id="admin-ai-list" class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
        <div class="px-3 py-4 text-center text-gray-400 text-xs">Memuat data...</div>
    </div>
</div>
