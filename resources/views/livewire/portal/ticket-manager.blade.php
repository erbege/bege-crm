<div class="space-y-6 pb-24" x-data="{ showCreateModal: false, showDetailModal: false }" 
    @open-detail-modal.window="showDetailModal = true"
    @close-modals.window="showCreateModal = false; showDetailModal = false">
    <div class="flex items-center space-x-4 mb-2">
        <button onclick="history.back()" class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </button>
        <h2 class="text-xl font-bold dark:text-white">Pusat Bantuan</h2>
    </div>

    <!-- Stats & Search -->
    <div class="space-y-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" class="block w-full pl-12 pr-4 py-4 bg-white dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800/60 rounded-3xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Cari tiket bantuan...">
        </div>

        <div class="flex items-center space-x-2 overflow-x-auto pb-2 -mx-4 px-4 no-scrollbar">
            <button wire:click="setStatusFilter('')" wire:loading.attr="disabled"
                class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap shadow-sm transition-all relative flex items-center justify-center min-w-[80px] {{ $statusFilter === '' ? 'bg-indigo-600 text-white ring-2 ring-indigo-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-800' }}">
                <span wire:loading.remove wire:target="setStatusFilter('')">Semua</span>
                <span wire:loading wire:target="setStatusFilter('')">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </button>
            <button wire:click="setStatusFilter('open')" wire:loading.attr="disabled"
                class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap flex items-center transition-all relative {{ $statusFilter === 'open' ? 'bg-indigo-600 text-white ring-2 ring-indigo-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-800' }}">
                <span wire:loading.remove wire:target="setStatusFilter('open')" class="flex items-center">
                    Terbuka
                    <span class="ml-2.5 px-2 py-0.5 {{ $statusFilter === 'open' ? 'bg-white/20 text-white' : 'bg-indigo-500/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400' }} text-[10px] font-black rounded-lg transition-colors">
                        {{ $openTicketsCount }}
                    </span>
                </span>
                <span wire:loading wire:target="setStatusFilter('open')">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </button>
            <button wire:click="setStatusFilter('in_progress')" wire:loading.attr="disabled"
                class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap flex items-center transition-all relative {{ $statusFilter === 'in_progress' ? 'bg-indigo-600 text-white ring-2 ring-indigo-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-800' }}">
                <span wire:loading.remove wire:target="setStatusFilter('in_progress')" class="flex items-center">
                    Diproses
                    <span class="ml-2.5 px-2 py-0.5 {{ $statusFilter === 'in_progress' ? 'bg-white/20 text-white' : 'bg-yellow-500/10 text-yellow-600 dark:bg-yellow-500/20 dark:text-yellow-400' }} text-[10px] font-black rounded-lg transition-colors">
                        {{ $inProgressTicketsCount }}
                    </span>
                </span>
                <span wire:loading wire:target="setStatusFilter('in_progress')">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </button>
            <button wire:click="setStatusFilter('resolved')" wire:loading.attr="disabled"
                class="px-5 py-2.5 rounded-full text-sm font-bold whitespace-nowrap flex items-center transition-all relative {{ $statusFilter === 'resolved' ? 'bg-indigo-600 text-white ring-2 ring-indigo-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-800' }}">
                <span wire:loading.remove wire:target="setStatusFilter('resolved')" class="flex items-center">
                    Selesai
                    <span class="ml-2.5 px-2 py-0.5 {{ $statusFilter === 'resolved' ? 'bg-white/20 text-white' : 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400' }} text-[10px] font-black rounded-lg transition-colors">
                        {{ $resolvedTicketsCount }}
                    </span>
                </span>
                <span wire:loading wire:target="setStatusFilter('resolved')">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
            </button>
        </div>
    </div>

    <!-- Create Button -->
    <button @click="showCreateModal = true" class="w-full py-5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-3xl shadow-xl shadow-indigo-500/30 flex items-center justify-center space-x-2 transition-all active:scale-[0.98]">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Buat Tiket Baru</span>
    </button>

    <!-- Ticket List -->
    <div class="relative min-h-[400px]">
        <div wire:loading.flex wire:target="setStatusFilter, search" class="absolute inset-0 z-10 items-center justify-center bg-white/50 dark:bg-slate-900/50 backdrop-blur-[2px] rounded-3xl">
            <div class="flex flex-col items-center space-y-2">
                <div class="h-8 w-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Memuat...</span>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($tickets as $ticket)
                <div class="bg-white dark:bg-slate-800/50 rounded-3xl p-5 border border-slate-100 dark:border-slate-800/60 space-y-4 group active:scale-[0.98] transition-all cursor-pointer"
                     @click="showDetailModal = true" wire:click="viewTicket({{ $ticket->id }})">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 rounded-2xl {{ $ticket->status === 'open' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' : 'bg-slate-100 text-slate-500 dark:bg-slate-900/50 dark:text-slate-400' }}">
                                @if($ticket->category?->name === 'Technical' || $ticket->category?->name === 'Teknis')
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.83-5.83m0 0a2.978 2.978 0 0 1-3.074-3.074m3.074 3.074 4.14 4.14a3.375 3.375 0 1 1-4.773 4.773l-4.14-4.14m-1.644-1.264A5.314 5.314 0 0 0 4.5 7.5C4.5 4.462 6.962 2 10.5 2c1.09 0 2.09.324 2.91.884m-4.99 10.32L3.25 21A2.652 2.652 0 0 1 1.5 17.25l5.83-5.83m3.074-3.074a4.994 4.994 0 0 0-5.86 1.03l3.074 3.074Z" />
                                    </svg>
                                @else
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75-3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V14.25" />
                                    </svg>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <h4 class="font-bold dark:text-white line-clamp-1">{{ $ticket->subject }}</h4>
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">#{{ $ticket->id }} • {{ $ticket->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            @php
                                $priorityColors = [
                                    'low' => 'text-slate-400',
                                    'medium' => 'text-amber-500',
                                    'high' => 'text-rose-500',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-900/50 {{ $priorityColors[$ticket->priority] ?? 'text-slate-400' }} uppercase tracking-widest">
                                <span class="h-1 w-1 rounded-full bg-current mr-1.5"></span>
                                {{ $ticket->priority }}
                            </span>
                        </div>
                        
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold {{ $ticket->status === 'open' ? 'bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400' : 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' }} border {{ $ticket->status === 'open' ? 'border-indigo-200 dark:border-indigo-500/20' : 'border-emerald-200 dark:border-emerald-500/20' }} uppercase tracking-widest">
                            {{ $ticket->status }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center space-y-3">
                    <div class="bg-slate-100 dark:bg-slate-800/50 w-20 h-20 rounded-full flex items-center justify-center mx-auto text-slate-400">
                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 1 0-2.636 6.364M16.5 12V8.25" />
                        </svg>
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada tiket bantuan</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Create Ticket Modal -->
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-0 pb-0 text-center sm:block">
            <div class="fixed inset-0 transition-opacity" @click="showCreateModal = false">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            
            <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-t-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-lg border-t border-slate-100 dark:border-slate-700/50">
                <form wire:submit="createTicket" class="px-6 pt-8 pb-20">
                    <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-8"></div>
                    <h3 class="text-xl font-bold dark:text-white mb-8">Buat Tiket Bantuan</h3>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest pl-1">Kategori</label>
                                <select wire:model="category_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 text-sm dark:text-white">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-xs text-red-500 pl-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest pl-1">Prioritas</label>
                                <select wire:model="priority" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 text-sm dark:text-white">
                                    <option value="low">Rendah (Low)</option>
                                    <option value="medium">Sedang (Medium)</option>
                                    <option value="high">Tinggi (High)</option>
                                </select>
                                @error('priority') <span class="text-xs text-red-500 pl-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest pl-1">Subjek</label>
                            <input type="text" wire:model="subject" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 text-sm dark:text-white" placeholder="Contoh: Internet Lambat">
                            @error('subject') <span class="text-xs text-red-500 pl-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest pl-1">Pesan</label>
                            <textarea wire:model="message" rows="4" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 text-sm dark:text-white resize-none" placeholder="Jelaskan kendala Anda..."></textarea>
                            @error('message') <span class="text-xs text-red-500 pl-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="pt-4 flex space-x-3">
                            <button type="button" @click="showCreateModal = false" class="flex-1 py-4 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-2xl">Batal</button>
                            <button type="submit" class="flex-2 px-4 py-4 bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/30">Kirim</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail/Reply Modal -->
    <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-end justify-center min-h-screen pt-4 px-0 pb-0 text-center sm:block">
            <div class="fixed inset-0 transition-opacity" @click="showDetailModal = false; $wire.closeTicket()">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            
            <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-t-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-lg border-t border-slate-100 dark:border-slate-700/50 h-[85vh] flex flex-col">
                <div class="px-6 pt-8 pb-4 shrink-0">
                    <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-8"></div>
                    @if($selectedTicket)
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <h3 class="text-xl font-bold dark:text-white">{{ $selectedTicket->subject }}</h3>
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">#{{ str_pad($selectedTicket->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 uppercase tracking-widest">
                                {{ $selectedTicket->status }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6 no-scrollbar" 
                    x-ref="chatContainer"
                    x-init="
                        const scroll = () => { $el.scrollTop = $el.scrollHeight };
                        $watch('showDetailModal', value => { if(value) { setTimeout(scroll, 100); setTimeout(scroll, 400); } });
                        new MutationObserver(() => { setTimeout(scroll, 50) }).observe($el, { childList: true, subtree: true });
                    ">
                    @if($selectedTicket)
                        @foreach($selectedTicket->messages as $msg)
                            <div class="flex flex-col {{ $msg->customer_id ? 'items-end' : 'items-start' }} space-y-2">
                                <div class="max-w-[85%] {{ $msg->customer_id ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-slate-100 dark:bg-slate-900/50 dark:text-slate-200 rounded-tl-none' }} rounded-2xl p-4">
                                    <p class="text-sm whitespace-pre-wrap">{{ $msg->message }}</p>
                                    <span class="text-[10px] {{ $msg->customer_id ? 'text-indigo-200' : 'text-slate-400' }} mt-2 block">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="px-6 pt-4 pb-24 bg-white dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700/50 shrink-0">
                    @if($selectedTicket && $selectedTicket->status !== 'closed')
                        <form wire:submit="sendReply" class="relative">
                            <input type="text" wire:model="replyMessage" class="w-full pl-5 pr-14 py-4 bg-slate-50 dark:bg-slate-900/50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500/20 text-sm dark:text-white" placeholder="Tulis balasan...">
                            <button type="submit" class="absolute right-2 top-2 p-2.5 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-500/30">
                                <!-- <svg class="h-5 w-5 rotate-90" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12.75 12 18.75 18 12.75M12 18.75V3" />
                                </svg>  -->
                                <svg class="h-5 w-5 transform rotate-90 ml-1" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                </svg>
                            </button>
                        </form>
                    @else
                        <div class="text-center py-3 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Tiket ini telah ditutup.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>