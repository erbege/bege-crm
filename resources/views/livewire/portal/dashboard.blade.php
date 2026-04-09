<div class="space-y-6" wire:init="fetchConnectionStatus">
    <!-- Service Status Card -->
    <div
        class="bg-white dark:bg-slate-800/50 rounded-3xl shadow-xl dark:shadow-none border border-slate-100 dark:border-slate-800/60 p-6 relative overflow-hidden group transition-all duration-300">
        <div class="absolute top-0 right-0 p-4">
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">ID:
                {{ $customer->customer_id ?? '88291034' }}</span>
        </div>

        <div class="flex flex-col space-y-4">
            <div>
                @if($isLoadingStatus)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 border border-slate-200 dark:border-slate-800 uppercase tracking-widest animate-pulse">
                        MENARIK DATA...
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold {{ ($connectionStatus['is_online'] ?? false) ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20' : 'bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20' }} uppercase tracking-widest">
                        <span
                            class="h-1.5 w-1.5 rounded-full {{ ($connectionStatus['is_online'] ?? false) ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }} mr-2"></span>
                        {{ ($connectionStatus['is_online'] ?? false) ? 'ONLINE' : 'OFFLINE' }}
                    </span>
                @endif
            </div>

            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Paket Internet</p>
                    <h2 class="text-2xl font-bold dark:text-white">
                        {{ $activeSubscription->package->name ?? 'Home Fiber 50 Mbps' }}
                    </h2>
                </div>
                <div
                    class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-2xl border border-slate-100 dark:border-slate-800 transition-transform group-hover:scale-110">
                    <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 0 1 1.06 0Z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Bottom Decor -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-indigo-500">
        </div>
    </div>

    <!-- Monthly Bill Card -->
    <div
        class="bg-white dark:bg-slate-800/50 rounded-3xl shadow-xl dark:shadow-none border border-slate-100 dark:border-slate-800/60 p-6 space-y-6">
        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
            <span class="text-sm font-medium">Tagihan Bulan Ini</span>
            <svg class="h-5 w-5 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
        </div>

        <div class="space-y-2">
            <h3 class="text-4xl font-black dark:text-white tracking-tight">
                <span
                    class="text-lg font-bold text-slate-400 dark:text-slate-500 mr-1">Rp</span>{{ number_format($recentInvoices->first()->total ?? 325000, 0, ',', '.') }}
            </h3>
            <div class="flex items-center text-rose-500 font-bold text-xs space-x-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                </svg>
                <span>Jatuh tempo:
                    {{ optional($recentInvoices->first())->due_date ? $recentInvoices->first()->due_date->format('d M Y') : '20 Okt 2023' }}</span>
            </div>
        </div>

        <a href="{{ route('portal.invoices') }}"
            class="flex items-center justify-center w-full py-4 px-6 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-500/30 transition-all duration-300">
            Bayar Sekarang
            <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </div>

    <!-- Data Usage Section -->
    <div class="space-y-4 pt-2">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold dark:text-white">Penggunaan Data</h3>
            <button
                class="text-sm font-bold text-indigo-600 dark:text-indigo-400 px-3 py-1 bg-indigo-100 dark:bg-indigo-500/10 rounded-lg">Lihat
                Detail</button>
        </div>

        <div
            class="bg-white dark:bg-slate-800/50 rounded-3xl shadow-xl dark:shadow-none border border-slate-100 dark:border-slate-800/60 p-6 flex items-center space-x-8">
            <div class="relative flex items-center justify-center h-28 w-28 shrink-0">
                <svg class="h-full w-full rotate-[-90deg]">
                    <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="12" fill="transparent"
                        class="text-slate-100 dark:text-slate-800" />
                    <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="12" fill="transparent"
                        stroke-dasharray="301" stroke-dashoffset="75" stroke-linecap="round"
                        class="text-indigo-600 dark:text-indigo-400 drop-shadow-[0_0_8px_rgba(79,70,229,0.4)]" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-lg font-black dark:text-white">75%</span>
                </div>
            </div>

            <div class="flex-1 space-y-4">
                <div class="space-y-2">
                    <div
                        class="flex justify-between text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <span>Terpakai</span>
                        <span class="dark:text-white">450 GB</span>
                    </div>
                    <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full w-3/4 bg-indigo-600 rounded-full"></div>
                    </div>
                </div>

                <div class="space-y-1">
                    <div
                        class="flex justify-between text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <span>Sisa Kuota</span>
                        <span class="dark:text-white uppercase">Unlimited</span>
                    </div>
                    <div class="h-1.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Speed Metrics -->
    <div class="grid grid-cols-3 gap-3">
        <div
            class="bg-white dark:bg-slate-800/50 rounded-3xl shadow-xl dark:shadow-none border border-slate-100 dark:border-slate-800/60 p-4 flex flex-col items-center justify-center space-y-2 text-center">
            <div class="text-emerald-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </div>
            <div class="space-y-0.5">
                <p class="text-2xl font-black dark:text-white">50</p>
                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Mbps DL
                </p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800/50 rounded-3xl shadow-xl dark:shadow-none border border-slate-100 dark:border-slate-800/60 p-4 flex flex-col items-center justify-center space-y-2 text-center">
            <div class="text-indigo-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                </svg>
            </div>
            <div class="space-y-0.5">
                <p class="text-2xl font-black dark:text-white">10</p>
                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Mbps UL
                </p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-800/50 rounded-3xl shadow-xl dark:shadow-none border border-slate-100 dark:border-slate-800/60 p-4 flex flex-col items-center justify-center space-y-2 text-center">
            <div class="text-amber-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                </svg>
            </div>
            <div class="space-y-0.5">
                <p class="text-2xl font-black dark:text-white">12</p>
                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">ms Ping
                </p>
            </div>
        </div>
    </div>

    <!-- Promo Card -->
    <div
        class="relative rounded-3xl overflow-hidden aspect-[16/9] shadow-2xl group cursor-pointer transition-transform duration-500">
        <img src="https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&q=80&w=800"
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            alt="Promo">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 p-8 space-y-2">
            <span
                class="inline-block px-3 py-1 bg-indigo-600 text-white text-[10px] font-bold uppercase tracking-widest rounded-lg">PROMO
                BARU</span>
            <h3 class="text-2xl font-bold text-white tracking-tight">Upgrade ke 100 Mbps</h3>
            <p class="text-sm text-slate-300">Nikmati streaming 4K tanpa buffer sekarang.</p>
        </div>
    </div>
</div>