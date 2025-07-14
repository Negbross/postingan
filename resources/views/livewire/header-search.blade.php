<div class="bg-white rounded-lg shadow-sm p-6 mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        {{-- Search Input --}}
        <div class="flex-1 max-w-md">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Cari..."
                >
            </div>
        </div>

        {{-- Sort Options --}}
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-700">Urutkan:</span>
            @foreach($sortOptions as $field => $label)
                <button
                    type="button"
                    wire:click="sortby('{{ $field }}')"
                    class="text-sm {{ $sortBy === $field ? 'text-blue-600 font-medium' : 'text-gray-500 hover:text-gray-700' }}"
                >
                    {{ $label }}
                    @if($sortBy === $field)
                        <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    @endif
                </button>
            @endforeach

        </div>
    </div>

    <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-gray-500">
        {{ $totals }}
    </div>
</div>
