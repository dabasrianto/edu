<div class="bg-white p-3 rounded-xl shadow-sm flex items-center justify-between border border-gray-100">
    <div class="flex items-center space-x-3">
        <span class="font-bold text-gray-400 w-6 text-center text-sm">#{{ $currentRank }}</span>
        <img src="{{ $rank->user->avatar ? Storage::url($rank->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($rank->user->name).'&background=random&size=32' }}" class="w-8 h-8 rounded-full object-cover">
        <span class="font-medium text-gray-700 text-sm line-clamp-1">{{ $rank->user->name }}</span>
    </div>
    <span class="font-bold text-blue-600 text-sm">{{ $rank->total_score }} Poin</span>
</div>
