<!-- User Management Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 flex items-center text-sm">
            @svg('heroicon-s-users', 'w-4 h-4 mr-2 text-blue-500')
            Kelola User
        </h3>
        <div class="flex items-center space-x-2">
            <input type="text" id="user-search" placeholder="Cari nama/email..." onkeyup="debounceUserSearch()" class="text-[10px] border border-gray-300 rounded px-2 py-1 w-32">
            <button onclick="loadUsers()" class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded hover:bg-blue-100">Refresh</button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase">User</th>
                    <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase text-center">Role</th>
                    <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase text-center">Aktif</th>
                    <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase text-right">Saldo</th>
                </tr>
            </thead>
            <tbody id="admin-user-list" class="divide-y divide-gray-50 text-xs">
                <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>
