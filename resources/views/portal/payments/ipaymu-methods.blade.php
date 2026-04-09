<x-layouts.portal>
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50">
                <div class="p-8 sm:p-12">
                    <!-- Header Section -->
                    <div class="flex items-center justify-between mb-10">
                        <div>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Metode
                                Pembayaran</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium italic">Silakan pilih metode
                                pembayaran iPaymu untuk Tagihan #{{ $invoice->invoice_number }}</p>
                        </div>
                        <div class="text-right">
                            <span
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Total
                                Bayar</span>
                            <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">Rp
                                {{ number_format((float) $invoice->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @php
                        // Normalize iPaymu data
                        // The API returns Data[] -> Groups with Channels[]
                        $groupedResults = collect();

                        if (isset($channels) && is_array($channels)) {
                            foreach ($channels as $group) {
                                if (isset($group['Channels']) && is_array($group['Channels'])) {
                                    $groupName = match ($group['Code'] ?? '') {
                                        'va' => 'Virtual Account',
                                        'cstore' => 'Convenience Store',
                                        'qris' => 'QRIS',
                                        default => $group['Name'] ?? 'Lainnya'
                                    };

                                    $items = collect($group['Channels'])->map(function ($ch) use ($group) {
                                        return [
                                            'method' => $group['Code'] ?? 'va',
                                            'channel' => $ch['Code'] ?? '',
                                            'name' => $ch['Name'] ?? 'Pembayaran',
                                            'icon' => $ch['Logo'] ?? null,
                                            'fee' => $ch['TransactionFee']['Fee'] ?? 0,
                                            'feeType' => $ch['TransactionFee']['Type'] ?? 'FLAT'
                                        ];
                                    });

                                    $groupedResults->put($groupName, $items);
                                }
                            }
                        }

                        // If no results from API, use our fallback logic (simplified)
                        if ($groupedResults->isEmpty()) {
                            $fallback = [
                                ['method' => 'va', 'channel' => 'bri', 'name' => 'BRI Virtual Account'],
                                ['method' => 'va', 'channel' => 'bni', 'name' => 'BNI Virtual Account'],
                                ['method' => 'va', 'channel' => 'mandiri', 'name' => 'Mandiri Virtual Account'],
                                ['method' => 'va', 'channel' => 'bca', 'name' => 'BCA Virtual Account'],
                                ['method' => 'cstore', 'channel' => 'alfamart', 'name' => 'Alfamart'],
                                ['method' => 'qris', 'channel' => 'qris', 'name' => 'QRIS']
                            ];
                            $groupedResults = collect($fallback)->map(function ($item) {
                                return array_merge($item, ['icon' => null, 'fee' => 0, 'feeType' => 'FLAT']);
                            })->groupBy(function ($item) {
                                return match ($item['method']) {
                                    'va' => 'Virtual Account',
                                    'cstore' => 'Convenience Store',
                                    'qris' => 'QRIS',
                                    default => 'Lainnya'
                                };
                            });
                        }

                        $icons = [
                            'bri' => 'https://upload.wikimedia.org/wikipedia/commons/2/2e/BRI_Logo.svg',
                            'bni' => 'https://upload.wikimedia.org/wikipedia/id/5/55/BNI_logo_2011.svg',
                            'mandiri' => 'https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg',
                            'bca' => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg',
                            'alfamart' => 'https://upload.wikimedia.org/wikipedia/commons/8/86/Alfamart_logo.svg',
                            'indomaret' => 'https://upload.wikimedia.org/wikipedia/commons/9/9d/Logo_Indomaret.png',
                            'qris' => 'https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg',
                        ];
                    @endphp

                    <div class="space-y-10">
                        @foreach($groupedResults as $group => $items)
                            <div>
                                <h4
                                    class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4 flex items-center gap-3">
                                    <span>{{ $group }}</span>
                                    <div class="h-[1px] flex-1 bg-gray-100 dark:bg-gray-700/50"></div>
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($items as $channel)
                                        <a href="{{ route('portal.invoices.pay', ['invoice' => $invoice->id, 'method' => $channel['method'], 'channel' => $channel['channel']]) }}"
                                            class="group relative bg-gray-50 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700/50 rounded-3xl p-5 transition-all duration-300 hover:bg-white dark:hover:bg-gray-800 hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-500/50 active:scale-[0.98]">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-16 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center p-2 shadow-sm border border-gray-100 dark:border-gray-700/50 group-hover:border-indigo-100 dark:group-hover:border-indigo-900/30 transition-colors">
                                                    @php
                                                        $iconUrl = $channel['icon'] ?? $icons[$channel['channel']] ?? null;
                                                    @endphp
                                                    @if($iconUrl)
                                                        <img src="{{ $iconUrl }}" alt="{{ $channel['name'] }}"
                                                            class="max-w-full max-h-full object-contain filter dark:brightness-90">
                                                    @else
                                                        <div class="text-indigo-600 font-bold text-xs uppercase">
                                                            {{ substr($channel['channel'], 0, 3) }}</div>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <h5
                                                        class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                        {{ $channel['name'] }}</h5>
                                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium mt-0.5 uppercase tracking-tighter">
                                                        @if($channel['fee'] > 0)
                                                            Biaya: {{ $channel['feeType'] == 'PERCENT' ? $channel['fee'].'%' : 'Rp'.number_format($channel['fee'], 0, ',', '.') }}
                                                        @else
                                                            Biaya Admin Sesuai Sistem
                                                        @endif
                                                    </p>
                                                </div>
                                                <div
                                                    class="text-gray-300 dark:text-gray-600 group-hover:text-indigo-500 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                            d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Footer / Back -->
                    <div
                        class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-700/50 flex justify-between items-center">
                        <a href="{{ route('portal.invoices') }}"
                            class="text-sm font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Tagihan
                        </a>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Powered
                                by</span>
                            <img src="https://ipaymu.com/wp-content/uploads/2021/01/logo-ipaymu.png" alt="iPaymu"
                                class="h-4 opacity-50 contrast-0 dark:invert">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.portal>