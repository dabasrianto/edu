<!-- Deposits Management Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 flex items-center text-sm">
            @svg('heroicon-s-banknotes', 'w-4 h-4 mr-2 text-lime-600')
            Kelola Deposit / Top-Up
        </h3>
        <button onclick="loadDeposits()" class="text-[10px] text-lime-600 font-bold bg-lime-50 px-2 py-1 rounded hover:bg-lime-100">Refresh</button>
    </div>
    <div id="admin-deposit-list" class="divide-y divide-gray-50 max-h-80 overflow-y-auto">
        <div class="px-3 py-4 text-center text-gray-400 text-xs">Memuat data...</div>
    </div>
</div>
