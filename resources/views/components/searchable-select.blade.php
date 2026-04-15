@props([
    'options' => [],
    'placeholder' => 'Select an option...',
    'name' => null,
])

<div x-data="{
    open: false,
    search: '',
    selectedId: @entangle($attributes->wire('model')),
    options: @if($attributes->has('wire:options')) @entangle($attributes->wire('options')) @else @js($options) @endif,
    
    get filteredOptions() {
        if (!this.search) return this.options;
        return this.options.filter(option => 
            option.name.toLowerCase().includes(this.search.toLowerCase()) || 
            (option.full_path && option.full_path.toLowerCase().includes(this.search.toLowerCase()))
        );
    },

    get selectedLabel() {
        let option = this.options.find(opt => opt.id == this.selectedId);
        return option ? option.name : '{{ $placeholder }}';
    },

    selectOption(id) {
        this.selectedId = id;
        this.open = false;
        this.search = '';
    }
}" class="relative">
    <button type="button" @click="open = !open"
        class="relative w-full px-4 py-3 text-left bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 flex items-center justify-between shadow-sm">
        <span class="block truncate font-medium" :class="{ 'text-gray-400': !selectedId }" x-text="selectedLabel"></span>
        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" @click.away="open = false" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute z-[60] w-full mt-2 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        
        <div class="p-3 border-b border-gray-50 dark:border-gray-800">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input x-model="search" 
                    @if($attributes->has('wire:search')) 
                        wire:model.live.debounce.300ms="{{ $attributes->get('wire:search') }}"
                    @endif
                    type="text" placeholder="Cari..."
                    class="block w-full pl-9 pr-3 py-2 bg-gray-50 dark:bg-gray-800/50 border-transparent rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>
        </div>

        <ul class="max-h-60 overflow-y-auto py-2 scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-gray-700">
            <template x-for="option in filteredOptions" :key="option.id">
                <li>
                    <button type="button" @click="selectOption(option.id)"
                        class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 transition-colors group flex flex-col justify-center">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" x-text="option.name"></span>
                        <template x-if="option.full_path">
                            <span class="text-[10px] text-gray-400 group-hover:text-indigo-400/80 transition-colors uppercase tracking-widest font-black" x-text="option.full_path"></span>
                        </template>
                    </button>
                </li>
            </template>
            <div x-show="filteredOptions.length === 0" class="px-4 py-8 text-center text-gray-400 italic text-xs">
                Tidak ada data ditemukan
            </div>
        </ul>
    </div>
</div>
