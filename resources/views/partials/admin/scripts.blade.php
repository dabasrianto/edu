{{-- Admin JavaScript for all AJAX operations --}}
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const adminHeaders = { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
const jsonHeaders = { ...adminHeaders, 'Content-Type': 'application/json' };

// ----- DASHBOARD -----
function loadDashboardStats() {
    fetch('/admin/api/stats', { headers: adminHeaders })
    .then(r => r.json()).then(d => {
        document.getElementById('stat-users').textContent = d.total_users ?? 0;
        document.getElementById('stat-enrollments').textContent = d.total_enrollments ?? 0;
        document.getElementById('stat-orders').textContent = d.total_orders ?? 0;
        document.getElementById('stat-pending-orders').textContent = d.pending_orders ?? 0;
        document.getElementById('stat-posts').textContent = d.total_posts ?? 0;
        document.getElementById('stat-quizzes').textContent = d.total_quizzes ?? 0;
        document.getElementById('stat-deposits').textContent = 'Rp ' + Number(d.total_deposits ?? 0).toLocaleString('id-ID');
    }).catch(() => {});
}

// ----- ENROLLMENTS -----
function fetchEnrollments() {
    fetch('/admin/api/enrollments', { headers: adminHeaders })
    .then(r => r.json()).then(enrolls => {
        const tbody = document.getElementById('admin-enrollment-list');
        if (!enrolls.length) { tbody.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-gray-400 text-xs">Tidak ada pendaftaran.</td></tr>'; return; }
        const statusColors = { pending:'bg-yellow-100 text-yellow-800', active:'bg-green-100 text-green-800', rejected:'bg-red-100 text-red-800' };
        tbody.innerHTML = enrolls.map(e => `
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2"><div class="font-semibold text-gray-800">${e.user?.name??'-'}</div><div class="text-[10px] text-gray-400">${e.user?.email??''}</div></td>
                <td class="px-3 py-2 text-xs font-semibold">${e.course?.title??'-'}</td>
                <td class="px-3 py-2">
                    <span class="text-[10px] bg-gray-100 px-1 py-0.5 rounded cursor-pointer" onclick="window.open('/storage/proofs/sample.jpg', '_blank')">Lihat Bukti</span>
                </td>
                <td class="px-3 py-2 text-center"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${statusColors[e.status]??'bg-gray-100'}">${e.status}</span></td>
                <td class="px-3 py-2 text-right">
                    ${e.status === 'pending' ? `
                    <div class="flex items-center justify-end gap-1">
                        <button onclick="updateEnrollStatus(${e.id},'active')" class="text-[10px] font-bold px-2 py-1 rounded bg-green-100 text-green-700 hover:bg-green-200">Terima</button>
                        <button onclick="updateEnrollStatus(${e.id},'rejected')" class="text-[10px] font-bold px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200">Tolak</button>
                    </div>` : '-'}
                </td>
            </tr>`).join('');
    }).catch(() => {});
}

function updateEnrollStatus(id, status) {
    if (!confirm(`Ubah status pendaftaran menjadi "${status}"?`)) return;
    fetch(`/admin/api/enrollments/${id}`, { method:'PUT', headers:jsonHeaders, body:JSON.stringify({status}) })
    .then(r=>r.json()).then(d=>{ if(d.success) fetchEnrollments(); else alert(d.message); }).catch(()=>alert('Gagal.'));
}

// ----- ORDERS -----
function loadOrders() {
    const status = document.getElementById('order-filter')?.value || 'all';
    fetch(`/admin/api/orders?status=${status}`, { headers: adminHeaders })
    .then(r => r.json()).then(orders => {
        const tbody = document.getElementById('admin-order-list');
        if (!orders.length) { tbody.innerHTML = '<tr><td colspan="5" class="px-3 py-4 text-center text-gray-400 text-xs">Tidak ada order.</td></tr>'; return; }
        const statusColors = { pending:'bg-yellow-100 text-yellow-800', processing:'bg-blue-100 text-blue-800', shipped:'bg-indigo-100 text-indigo-800', completed:'bg-green-100 text-green-800', cancelled:'bg-red-100 text-red-800' };
        tbody.innerHTML = orders.map(o => `
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2 font-mono text-[10px] text-gray-500">#${o.id}</td>
                <td class="px-3 py-2"><div class="font-semibold text-gray-800">${o.user?.name??'-'}</div><div class="text-[10px] text-gray-400">${o.user?.email??''}</div></td>
                <td class="px-3 py-2 font-semibold">Rp ${Number(o.total_amount).toLocaleString('id-ID')}</td>
                <td class="px-3 py-2 text-center"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${statusColors[o.status]??'bg-gray-100'}">${o.status}</span></td>
                <td class="px-3 py-2 text-right">
                    <select onchange="updateOrderStatus(${o.id}, this.value)" class="text-[10px] border rounded px-1 py-0.5 bg-white">
                        <option value="">Ubah...</option>
                        ${['pending','processing','shipped','completed','cancelled'].filter(s=>s!==o.status).map(s=>`<option value="${s}">${s}</option>`).join('')}
                    </select>
                </td>
            </tr>`).join('');
    }).catch(() => {});
}

function updateOrderStatus(id, status) {
    if (!status) return;
    if (!confirm(`Ubah status order #${id} menjadi "${status}"?`)) return;
    fetch(`/admin/api/orders/${id}`, { method:'PUT', headers:jsonHeaders, body:JSON.stringify({status}) })
    .then(r=>r.json()).then(d=>{ if(d.success) loadOrders(); else alert(d.message); }).catch(()=>alert('Gagal mengupdate.'));
}

// ----- POSTS -----
function loadPosts() {
    fetch('/admin/api/posts', { headers: adminHeaders })
    .then(r => r.json()).then(posts => {
        const container = document.getElementById('admin-post-list');
        if (!posts.length) { container.innerHTML = '<div class="px-3 py-4 text-center text-gray-400 text-xs">Belum ada artikel.</div>'; return; }
        const statusColors = { published:'bg-green-100 text-green-800', draft:'bg-yellow-100 text-yellow-800', archived:'bg-gray-200 text-gray-600' };
        container.innerHTML = posts.map(p => `
            <div class="px-4 py-3 hover:bg-gray-50 flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-sm text-gray-800 truncate">${p.title}</div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold ${statusColors[p.status]??''}">${p.status}</span>
                        <span class="text-[10px] text-gray-400">${p.category?.name??'Tanpa Kategori'}</span>
                    </div>
                </div>
                <button onclick="deletePost(${p.id})" class="text-red-400 hover:text-red-600 flex-shrink-0" title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>`).join('');
    }).catch(() => {});
}

function togglePostForm() {
    const c = document.getElementById('post-form-container');
    c.classList.toggle('hidden');
    if (!c.classList.contains('hidden')) loadCategories();
}

function loadCategories() {
    fetch('/admin/api/categories', { headers: adminHeaders })
    .then(r => r.json()).then(cats => {
        const sel = document.getElementById('post-category-select');
        sel.innerHTML = '<option value="">-- Kategori --</option>' + cats.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    }).catch(() => {});
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('add-post-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        fetch('/admin/api/posts', { method:'POST', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, body:fd })
        .then(r=>r.json()).then(d=>{ if(d.success){ this.reset(); togglePostForm(); loadPosts(); } else alert(d.message??'Gagal.'); })
        .catch(()=>alert('Gagal menyimpan.'));
    });
});

function deletePost(id) {
    if (!confirm('Yakin hapus artikel ini?')) return;
    fetch(`/admin/api/posts/${id}`, { method:'DELETE', headers:jsonHeaders })
    .then(r=>r.json()).then(d=>{ if(d.success) loadPosts(); else alert(d.message); }).catch(()=>alert('Gagal menghapus.'));
}

// ----- QUIZZES -----
function loadQuizzes() {
    fetch('/admin/api/quizzes', { headers: adminHeaders })
    .then(r => r.json()).then(quizzes => {
        const container = document.getElementById('admin-quiz-list');
        if (!quizzes.length) { container.innerHTML = '<div class="px-3 py-4 text-center text-gray-400 text-xs">Belum ada quiz.</div>'; return; }
        container.innerHTML = quizzes.map(q => `
            <div class="px-4 py-3 hover:bg-gray-50 flex items-center justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-sm text-gray-800 truncate">${q.title}</div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold ${q.type==='wajib'?'bg-blue-100 text-blue-800':'bg-green-100 text-green-800'}">${q.type}</span>
                        <span class="text-[10px] text-gray-400">${q.questions_count??0} soal</span>
                        <span class="text-[10px] text-gray-400">${q.duration_minutes??'-'} mnt</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button onclick="toggleQuizField(${q.id},'is_active',${q.is_active?'false':'true'})" class="text-[10px] font-bold px-2 py-1 rounded ${q.is_active?'bg-green-100 text-green-700':'bg-gray-200 text-gray-500'}" title="Toggle Active">${q.is_active?'Aktif':'Nonaktif'}</button>
                    <button onclick="toggleQuizField(${q.id},'show_result',${q.show_result?'false':'true'})" class="text-[10px] font-bold px-2 py-1 rounded ${q.show_result?'bg-blue-100 text-blue-700':'bg-gray-200 text-gray-500'}" title="Toggle Show Result">${q.show_result?'Hasil':'Sembunyi'}</button>
                    <button onclick="deleteQuiz(${q.id})" class="text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </div>
            </div>`).join('');
    }).catch(() => {});
}

function toggleQuizField(id, field, value) {
    fetch(`/admin/api/quizzes/${id}`, { method:'PUT', headers:jsonHeaders, body:JSON.stringify({[field]:value}) })
    .then(r=>r.json()).then(d=>{ if(d.success) loadQuizzes(); }).catch(()=>{});
}

function deleteQuiz(id) {
    if (!confirm('Yakin hapus quiz ini?')) return;
    fetch(`/admin/api/quizzes/${id}`, { method:'DELETE', headers:jsonHeaders })
    .then(r=>r.json()).then(d=>{ if(d.success) loadQuizzes(); else alert(d.message); }).catch(()=>alert('Gagal menghapus.'));
}

// ----- AI SETTINGS -----
function loadAiSettings() {
    fetch('/admin/api/ai-settings', { headers: adminHeaders })
    .then(r => r.json()).then(settings => {
        const container = document.getElementById('admin-ai-list');
        if (!settings.length) { container.innerHTML = '<div class="px-3 py-4 text-center text-gray-400 text-xs">Belum ada AI setting.</div>'; return; }
        const providerColors = { gemini:'bg-blue-100 text-blue-800', openai:'bg-emerald-100 text-emerald-800', groq:'bg-orange-100 text-orange-800', qwen:'bg-purple-100 text-purple-800' };
        container.innerHTML = settings.map(s => `
            <div class="px-4 py-3 hover:bg-gray-50 flex items-center justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold ${providerColors[s.provider]??'bg-gray-100'}">${s.provider}</span>
                        <span class="text-sm font-semibold text-gray-800">${s.selected_model??'-'}</span>
                    </div>
                    <div class="text-[10px] text-gray-400 mt-1 truncate">API: ${s.api_key?s.api_key.substring(0,12)+'***':'N/A'}</div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button onclick="toggleAiActive(${s.id},${s.is_active?'false':'true'})" class="text-[10px] font-bold px-2 py-1 rounded ${s.is_active?'bg-green-100 text-green-700':'bg-gray-200 text-gray-500'}">${s.is_active?'Aktif':'Off'}</button>
                    <button onclick="deleteAiSetting(${s.id})" class="text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                </div>
            </div>`).join('');
    }).catch(() => {});
}

function toggleAiForm() { document.getElementById('ai-form-container').classList.toggle('hidden'); }
function toggleAiActive(id, val) {
    fetch(`/admin/api/ai-settings/${id}`, { method:'PUT', headers:jsonHeaders, body:JSON.stringify({is_active:val}) })
    .then(r=>r.json()).then(d=>{ if(d.success) loadAiSettings(); }).catch(()=>{});
}
function deleteAiSetting(id) {
    if (!confirm('Yakin hapus AI setting ini?')) return;
    fetch(`/admin/api/ai-settings/${id}`, { method:'DELETE', headers:jsonHeaders })
    .then(r=>r.json()).then(d=>{ if(d.success) loadAiSettings(); }).catch(()=>alert('Gagal.'));
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('add-ai-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(this));
        fetch('/admin/api/ai-settings', { method:'POST', headers:jsonHeaders, body:JSON.stringify(data) })
        .then(r=>r.json()).then(d=>{ if(d.success){ this.reset(); toggleAiForm(); loadAiSettings(); } else alert(d.message??'Gagal.'); })
        .catch(()=>alert('Gagal.'));
    });
});

// ----- USERS -----
let userSearchTimer;
function debounceUserSearch() { clearTimeout(userSearchTimer); userSearchTimer = setTimeout(loadUsers, 400); }
function loadUsers() {
    const search = document.getElementById('user-search')?.value || '';
    fetch(`/admin/api/users?search=${encodeURIComponent(search)}`, { headers: adminHeaders })
    .then(r => r.json()).then(users => {
        const tbody = document.getElementById('admin-user-list');
        if (!users.length) { tbody.innerHTML = '<tr><td colspan="4" class="px-3 py-4 text-center text-gray-400 text-xs">Tidak ada user.</td></tr>'; return; }
        tbody.innerHTML = users.map(u => `
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2"><div class="font-semibold text-gray-800">${u.name}</div><div class="text-[10px] text-gray-400">${u.email}</div></td>
                <td class="px-3 py-2 text-center"><span class="px-1.5 py-0.5 rounded text-[10px] font-bold ${u.role==='admin'?'bg-red-100 text-red-800':'bg-gray-100 text-gray-600'}">${u.role}</span></td>
                <td class="px-3 py-2 text-center">
                    <button onclick="toggleUserActive(${u.id},${u.is_active?'false':'true'})" class="text-[10px] font-bold rounded-full w-12 py-0.5 ${u.is_active?'bg-green-100 text-green-700':'bg-red-100 text-red-700'}">${u.is_active?'Ya':'No'}</button>
                </td>
                <td class="px-3 py-2 text-right text-[10px] font-mono">Rp ${Number(u.balance??0).toLocaleString('id-ID')}</td>
            </tr>`).join('');
    }).catch(() => {});
}

function toggleUserActive(id, val) {
    fetch(`/admin/api/users/${id}`, { method:'PUT', headers:jsonHeaders, body:JSON.stringify({is_active:val}) })
    .then(r=>r.json()).then(d=>{ if(d.success) loadUsers(); }).catch(()=>{});
}

// ----- DEPOSITS -----
function loadDeposits() {
    fetch('/admin/api/deposits', { headers: adminHeaders })
    .then(r => r.json()).then(deps => {
        const container = document.getElementById('admin-deposit-list');
        if (!deps.length) { container.innerHTML = '<div class="px-3 py-4 text-center text-gray-400 text-xs">Belum ada deposit.</div>'; return; }
        const statusColors = { pending:'bg-yellow-100 text-yellow-800', approved:'bg-green-100 text-green-800', rejected:'bg-red-100 text-red-800' };
        container.innerHTML = deps.map(d => `
            <div class="px-4 py-3 hover:bg-gray-50 flex items-center justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-sm text-gray-800">${d.user?.name??'-'}</div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="font-bold text-sm text-emerald-700">Rp ${Number(d.amount).toLocaleString('id-ID')}</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold ${statusColors[d.status]??''}">${d.status}</span>
                    </div>
                    <div class="text-[10px] text-gray-400">${d.bank_account?.bank_name??'-'} • ${new Date(d.created_at).toLocaleDateString('id-ID')}</div>
                </div>
                ${d.status === 'pending' ? `
                <div class="flex items-center gap-1 flex-shrink-0">
                    <button onclick="updateDeposit(${d.id},'approved')" class="text-[10px] font-bold px-2 py-1 rounded bg-green-100 text-green-700 hover:bg-green-200">✓</button>
                    <button onclick="updateDeposit(${d.id},'rejected')" class="text-[10px] font-bold px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200">✗</button>
                </div>` : ''}
            </div>`).join('');
    }).catch(() => {});
}

function updateDeposit(id, status) {
    if (!confirm(`${status==='approved'?'Approve':'Reject'} deposit ini?`)) return;
    fetch(`/admin/api/deposits/${id}`, { method:'PUT', headers:jsonHeaders, body:JSON.stringify({status}) })
    .then(r=>r.json()).then(d=>{ if(d.success){ loadDeposits(); loadDashboardStats(); } else alert(d.message); }).catch(()=>alert('Gagal.'));
}

// ----- INIT: Load all when admin tab visible -----
function initAdminSections() {
    loadDashboardStats();
    fetchEnrollments();
    loadOrders();
    loadPosts();
    loadQuizzes();
    loadAiSettings();
    loadUsers();
    loadDeposits();
}

// Auto-init when admin tab is shown
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'admin') {
        setTimeout(initAdminSections, 300);
    }
});

// Also listen for tab switching
const origSwitchTab = window.switchTab;
if (typeof origSwitchTab === 'function') {
    window.switchTab = function(tab) {
        origSwitchTab(tab);
        if (tab === 'admin') setTimeout(initAdminSections, 300);
    };
}
</script>
