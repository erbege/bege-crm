<x-layouts.portal>
    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-[2.5rem] border border-gray-100 dark:border-gray-700/50">
                <div class="p-8 sm:p-12">
                    <!-- Header Section -->
                    <div class="flex items-center justify-between mb-10">
                        <div>
                            <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Metode Pembayaran</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium italic">Silakan pilih metode pembayaran Duitku untuk Tagihan #{{ $invoice->invoice_number }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Total Bayar</span>
                            <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">Rp {{ number_format((float) $invoice->total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @php
                        $normalizedChannels = collect($channels)->map(function($item) {
                            $code = $item['method'] ?? $item['paymentMethod'] ?? '';
                            $name = $item['name'] ?? $item['paymentName'] ?? 'Pembayaran';
                            
                            $group = 'Lainnya';
                            if (str_contains($name, 'Virtual Account') || in_array($code, ['BC', 'BR', 'M2', 'B1', 'BT', 'A1', 'NC'])) {
                                $group = 'Virtual Account';
                            } elseif (in_array($code, ['DA', 'SP', 'OV', 'LA', 'DQ', 'LQ'])) {
                                $group = 'E-Wallet & QRIS';
                            } elseif (in_array($code, ['I1', 'AL', 'FT'])) {
                                $group = 'Convenience Store';
                            } elseif ($code == 'VC') {
                                $group = 'Kartu Kredit';
                            }

                            return [
                                'code' => $code,
                                'name' => $name,
                                'group' => $group,
                                'fee' => $item['paymentFee'] ?? 0
                            ];
                        });
                        $groupedChannels = $normalizedChannels->groupBy('group');

                        $icons = [
                            'BC' => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg',
                            'BR' => 'https://upload.wikimedia.org/wikipedia/commons/2/2e/BRI_Logo.svg',
                            'M2' => 'https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg',
                            'B1' => 'https://upload.wikimedia.org/wikipedia/id/5/55/BNI_logo_2011.svg',
                            'BT' => 'https://upload.wikimedia.org/wikipedia/id/b/bf/Permata_Bank_logo.svg',
                            'A1' => 'https://upload.wikimedia.org/wikipedia/commons/3/36/Logo_ATM_Bersama.svg',
                            'NC' => 'https://upload.wikimedia.org/wikipedia/id/5/51/Bank_Neo_Commerce_logo.svg',
                            'I1' => 'https://upload.wikimedia.org/wikipedia/commons/9/9d/Logo_Indomaret.png',
                            'AL' => 'https://upload.wikimedia.org/wikipedia/commons/8/86/Alfamart_logo.svg',
                            'DA' => 'https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg',
                            'SP' => 'https://upload.wikimedia.org/wikipedia/commons/f/fe/ShopeePay.svg',
                            'OV' => 'https://upload.wikimedia.org/wikipedia/commons/d/d1/OVO_Logo.svg',
                            'LQ' => 'https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg',
                            'DQ' => 'https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg',
                            'VC' => 'https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg',
                            'LA' => 'https://upload.wikimedia.org/wikipedia/commons/6/6d/LinkAja.svg',
                        ];
                    @endphp

                    <div class="space-y-10">
                        @foreach($groupedChannels as $group => $items)
                            <div>
                                <h4 class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4 flex items-center gap-3">
                                    <span>{{ $group }}</span>
                                    <div class="h-[1px] flex-1 bg-gray-100 dark:bg-gray-700/50"></div>
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($items as $channel)
                                        <a href="{{ route('portal.invoices.pay', ['invoice' => $invoice->id, 'method' => $channel['code']]) }}" 
                                           class="group relative bg-gray-50 dark:bg-gray-900/30 border border-gray-100 dark:border-gray-700/50 rounded-3xl p-5 transition-all duration-300 hover:bg-white dark:hover:bg-gray-800 hover:shadow-xl hover:shadow-indigo-500/10 hover:border-indigo-500/50 active:scale-[0.98]">
                                            <div class="flex items-center gap-4">
                                                <div class="w-16 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center p-2 shadow-sm border border-gray-100 dark:border-gray-700/50 group-hover:border-indigo-100 dark:group-hover:border-indigo-900/30 transition-colors">
                                                    @if(isset($icons[$channel['code']]))
                                                        <img src="{{ $icons[$channel['code']] }}" alt="{{ $channel['name'] }}" class="max-w-full max-h-full object-contain filter dark:brightness-90">
                                                    @else
                                                        <div class="text-indigo-600 font-bold text-xs uppercase">{{ substr($channel['code'], 0, 3) }}</div>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <h5 class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $channel['name'] }}</h5>
                                                    @if($channel['fee'] > 0)
                                                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium mt-0.5">Biaya: Rp {{ number_format($channel['fee'], 0, ',', '.') }}</p>
                                                    @else
                                                        <p class="text-[10px] text-indigo-500 font-medium mt-0.5 uppercase tracking-tighter">Biaya Admin Sesuai Sistem</p>
                                                    @endif
                                                </div>
                                                <div class="text-gray-300 dark:text-gray-600 group-hover:text-indigo-500 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
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
                    <div class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-700/50 flex justify-between items-center">
                        <a href="{{ route('portal.invoices') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Tagihan
                        </a>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Powered by</span>
                            <img src="https://duitku.com/wp-content/uploads/2021/01/duitku-logo.png" alt="Duitku" class="h-4 opacity-50 contrast-0 dark:invert">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.portal>
