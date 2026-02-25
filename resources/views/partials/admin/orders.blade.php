<!-- Order Management Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-bold text-gray-800 flex items-center text-sm">
            @svg('heroicon-s-shopping-bag', 'w-4 h-4 mr-2 text-orange-500')
            Kelola Order
        </h3>
        <div class="flex items-center space-x-2">
            <select id="order-filter" onchange="loadOrders()" class="text-[10px] border border-gray-300 rounded px-2 py-1 bg-white">
                <option value="all">Semua</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <button onclick="loadOrders()" class="text-[10px] text-orange-600 font-bold bg-orange-50 px-2 py-1 rounded hover:bg-orange-100">Refresh</button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase">ID</th>
                    <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase">Customer</th>
                    <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase">Total</th>
                    <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase text-center">Status</th>
                    <th class="px-3 py-2 text-[10px] font-bold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody id="admin-order-list" class="divide-y divide-gray-50 text-xs">
                <tr><td colspan="5" class="px-3 py-4 text-center text-gray-400">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>
