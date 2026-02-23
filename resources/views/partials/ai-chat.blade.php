<!-- Floating AI Chatbot -->
<div id="ai-chatbot" style="position: fixed !important; bottom: 120px !important; right: 25px !important; z-index: 9999999 !important; display: block !important; pointer-events: auto !important;">
    <!-- Chat Toggle Button -->
    <button onclick="toggleAiChat()" id="ai-chat-toggle" style="width: 65px !important; height: 65px !important; background-color: #1e3a8a !important; color: white !important; border-radius: 50% !important; border: 4px solid white !important; display: flex !important; align-items: center !important; justify-content: center !important; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3) !important; cursor: pointer !important; transition: all 0.3s !important;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 40px !important; height: 40px !important;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
        </svg>
    </button>

    <!-- Chat Window -->
    <div id="ai-chat-window" class="hidden absolute bottom-16 right-0 w-[320px] md:w-[380px] bg-white rounded-2xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden animate-scale-up origin-bottom-right">
        <!-- Header -->
        <div class="bg-blue-900 p-4 text-white flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <span class="text-lg">🤖</span>
                </div>
                <div>
                    <h3 class="font-bold text-sm">HSI AI Assistant</h3>
                    <p class="text-[10px] text-blue-200">Tanya apa saja seputar materi</p>
                </div>
            </div>
            <button onclick="toggleAiChat()" class="p-1 hover:bg-white/10 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Configuration Bar Hidden (Managed by Admin) -->
        <div id="ai-config-info" class="px-4 py-2 bg-gray-50 border-b border-gray-100 text-[10px] text-gray-500 flex justify-between items-center">
            <span>Mode: Asisten HSI Edu</span>
            <span id="ai-active-provider" class="font-medium text-blue-700">Loading...</span>
        </div>

        <!-- Chat Area -->
        <div id="ai-chat-messages" class="flex-1 h-[350px] overflow-y-auto p-4 space-y-4 bg-gray-50/50">
            <!-- Bot Welcome -->
            <div class="flex items-start space-x-2">
                <div class="w-7 h-7 bg-blue-100 text-blue-800 rounded-lg flex items-center justify-center flex-shrink-0 text-xs shadow-sm">AI</div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[85%]">
                    <p class="text-xs text-gray-700 leading-relaxed">Assalamu'alaikum! Ada yang bisa saya bantu terkait silsilah materi HSI?</p>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white border-t border-gray-100">
            <div class="flex items-center space-x-2 bg-gray-50 rounded-xl p-1 pr-1 pl-3 border border-gray-100 focus-within:border-blue-300 transition-colors">
                <input type="text" id="ai-chat-input" placeholder="Tulis pesan..." class="flex-1 bg-transparent border-none focus:ring-0 text-sm py-2" onkeypress="if(event.key === 'Enter') sendAiMessage()">
                <button onclick="sendAiMessage()" id="ai-send-btn" class="bg-blue-900 text-white p-2 rounded-lg hover:bg-blue-800 transition-colors shadow-md active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                      <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                    </svg>
                </button>
            </div>
            <p class="text-[9px] text-center text-gray-400 mt-2">Ditenagai oleh Layanan AI HSI Edu</p>
        </div>
    </div>
</div>

<style>
    #ai-chat-messages::-webkit-scrollbar { width: 4px; }
    #ai-chat-messages::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    #ai-chat-messages::-webkit-scrollbar-track { background: transparent; }
</style>

<script>
    console.log("AI Chatbot Component Loaded");
    let activeSetting = null;
    let chatHistory = [];
    
    function toggleAiChat() {
        if (!window.isAuthenticated) {
            openAuthModal();
            return;
        }

        const windowEl = document.getElementById('ai-chat-window');
        const isOpening = windowEl.classList.contains('hidden');
        
        windowEl.classList.toggle('hidden');
        
        if (isOpening && !activeSetting) {
            loadAiSettings();
        }
    }

    async function loadAiSettings() {
        try {
            const res = await fetch('{{ route("ai.settings") }}');
            activeSetting = await res.json();
            
            const providerEl = document.getElementById('ai-active-provider');
            if (activeSetting && activeSetting.provider) {
                providerEl.textContent = activeSetting.provider;
            } else {
                providerEl.textContent = 'Belum dikonfigurasi';
            }
        } catch (e) {
            console.error("AI Settings Error", e);
        }
    }

    async function sendAiMessage() {
        const input = document.getElementById('ai-chat-input');
        const text = input.value.trim();
        const btn = document.getElementById('ai-send-btn');

        if (!text) return;
        if (!activeSetting || !activeSetting.id) {
            alert('Layanan AI belum siap atau belum dikonfigurasi di Admin.');
            return;
        }

        // Add user message to UI
        appendMessage('user', text);
        input.value = '';
        
        // Block interaction
        btn.disabled = true;
        btn.classList.add('opacity-50');
        
        // Add typing indicator
        const typingId = 'typing-' + Date.now();
        appendTypingIndicator(typingId);

        try {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            const res = await fetch('{{ route("ai.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    setting_id: activeSetting.id,
                    model: activeSetting.model,
                    message: text,
                    history: chatHistory
                })
            });

            // Remove typing indicator BEFORE processing result
            const typingEl = document.getElementById(typingId);
            if(typingEl) typingEl.remove();

            if (!res.ok) {
                const errorData = await res.json().catch(() => ({}));
                appendMessage('bot', 'Error Server: ' + (errorData.error || 'Terjadi kesalahan internal.'));
                return;
            }

            const data = await res.json();

            if (data.response) {
                appendMessage('bot', data.response);
                // Keep history (Max 6 for context window)
                chatHistory.push({ role: 'user', content: text });
                chatHistory.push({ role: 'assistant', content: data.response });
                if (chatHistory.length > 6) chatHistory.splice(0, 2);
            } else {
                appendMessage('bot', 'Maaf, terjadi kesalahan: ' + (data.error || 'Unknown error'));
            }
        } catch (e) {
            const typingEl = document.getElementById(typingId);
            if(typingEl) typingEl.remove();
            appendMessage('bot', 'Terjadi kesalahan koneksi.');
            console.error(e);
        } finally {
            btn.disabled = false;
            btn.classList.remove('opacity-50');
        }
    }

    function appendMessage(role, text) {
        const container = document.getElementById('ai-chat-messages');
        const div = document.createElement('div');
        
        if (role === 'user') {
            div.className = "flex justify-end";
            div.innerHTML = `
                <div class="bg-blue-900 text-white p-3 rounded-2xl rounded-tr-none shadow-sm max-w-[85%] text-xs leading-relaxed">
                    ${text}
                </div>
            `;
        } else {
            div.className = "flex items-start space-x-2";
            div.innerHTML = `
                <div class="w-7 h-7 bg-blue-100 text-blue-800 rounded-lg flex items-center justify-center flex-shrink-0 text-xs shadow-sm">AI</div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-gray-100 max-w-[85%] text-xs text-gray-700 leading-relaxed whitespace-pre-wrap">
                    ${text}
                </div>
            `;
        }
        
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    function appendTypingIndicator(id) {
        const container = document.getElementById('ai-chat-messages');
        const div = document.createElement('div');
        div.id = id;
        div.className = "flex items-start space-x-2";
        div.innerHTML = `
            <div class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="animate-bounce">...</span>
            </div>
            <div class="bg-gray-100 p-3 rounded-2xl rounded-tl-none max-w-[85%] text-[10px] text-gray-500 italic">
                Sedang mengetik...
            </div>
        `;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }
</script>
