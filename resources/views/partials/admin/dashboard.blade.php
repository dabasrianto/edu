<!-- Dashboard Stats Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 flex items-center text-sm">
            @svg('heroicon-s-chart-bar', 'w-4 h-4 mr-2 text-emerald-600')
            Dashboard
        </h3>
        <button onclick="loadDashboardStats()" class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-1 rounded hover:bg-emerald-100 transition-colors">Refresh</button>
    </div>
    <div id="dashboard-stats" class="p-4">
        <div class="grid grid-cols-2 gap-3" id="stats-grid">
            <div class="bg-blue-50 rounded-lg p-3 text-center"><div class="text-2xl font-bold text-blue-700" id="stat-users">-</div><div class="text-[10px] text-blue-600 font-semibold uppercase">Total User</div></div>
            <div class="bg-green-50 rounded-lg p-3 text-center"><div class="text-2xl font-bold text-green-700" id="stat-enrollments">-</div><div class="text-[10px] text-green-600 font-semibold uppercase">Pendaftaran</div></div>
            <div class="bg-orange-50 rounded-lg p-3 text-center"><div class="text-2xl font-bold text-orange-700" id="stat-orders">-</div><div class="text-[10px] text-orange-600 font-semibold uppercase">Total Order</div></div>
            <div class="bg-purple-50 rounded-lg p-3 text-center"><div class="text-2xl font-bold text-purple-700" id="stat-posts">-</div><div class="text-[10px] text-purple-600 font-semibold uppercase">Artikel</div></div>
            <div class="bg-red-50 rounded-lg p-3 text-center"><div class="text-2xl font-bold text-red-700" id="stat-pending-orders">-</div><div class="text-[10px] text-red-600 font-semibold uppercase">Order Pending</div></div>
            <div class="bg-indigo-50 rounded-lg p-3 text-center"><div class="text-2xl font-bold text-indigo-700" id="stat-quizzes">-</div><div class="text-[10px] text-indigo-600 font-semibold uppercase">Total Quiz</div></div>
        </div>
        <div class="mt-3 bg-emerald-50 rounded-lg p-3 text-center">
            <div class="text-sm font-bold text-emerald-700" id="stat-deposits">Rp -</div>
            <div class="text-[10px] text-emerald-600 font-semibold uppercase">Total Deposit Approved</div>
        </div>
    </div>
</div>
