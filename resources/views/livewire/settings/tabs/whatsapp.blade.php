<div class="space-y-6">
    <!-- Provider Settings -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Pengaturan Provider</h3>
        
        <div class="space-y-4" x-data="{ openProvider: '{{ $whatsapp_provider }}' }">
            
            <!-- Fonnte -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openProvider = openProvider === 'fonnte' ? null : 'fonnte'"
                    class="w-full flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center gap-3">
                        <span class="font-medium text-gray-900 dark:text-gray-100">Fonnte</span>
                        @if($whatsapp_provider === 'fonnte')
                             <span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full">Aktif</span>
                        @endif
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transition-transform"
                        :class="{ 'rotate-180': openProvider === 'fonnte' }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openProvider === 'fonnte'" x-collapse
                    class="border-t border-gray-200 dark:border-gray-700">
                    <div class="p-4 space-y-4 bg-white dark:bg-gray-800">
                         <div class="flex items-center justify-between">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:click="setWhatsappProvider('fonnte')" @if($whatsapp_provider === 'fonnte') checked @endif class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Gunakan Fonnte</span>
                            </label>
                        </div>

                        @if($whatsapp_provider === 'fonnte')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Token API</label>
                                <input type="password" wire:model="whatsapp_token"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Qontak -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                <button @click="openProvider = openProvider === 'qontak' ? null : 'qontak'"
                    class="w-full flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <div class="flex items-center gap-3">
                         <span class="font-medium text-gray-900 dark:text-gray-100">Qontak (Coming Soon)</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transition-transform"
                        :class="{ 'rotate-180': openProvider === 'qontak' }" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                 <div x-show="openProvider === 'qontak'" x-collapse
                    class="border-t border-gray-200 dark:border-gray-700">
                     <div class="p-4 bg-white dark:bg-gray-800 text-sm text-gray-500">
                        Integrasi Qontak akan segera hadir.
                    </div>
                </div>
            </div>

             <div class="flex justify-end pt-4 gap-3">
                <button wire:click="testWhatsapp" type="button"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Tes Koneksi
                </button>
                <button wire:click="saveWhatsapp"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Simpan Konfigurasi
                </button>
            </div>
        </div>
    </div>

    <!-- Message Templates -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Template Pesan</h3>
            <div class="flex items-center gap-2">
                <button wire:click="resetDefaultTemplates"
                    class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md font-medium text-xs hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Muat Template Default
                </button>
                <button wire:click="openTemplateModal"
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-medium text-xs text-white hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Template
                </button>
            </div>
        </div>

        @if(count($templates) > 0)
            <div class="space-y-4" x-data="{ openTemplate: null }">
                @foreach($templates as $tpl)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 shadow-sm transition-all duration-300"
                        :class="{ 'ring-1 ring-indigo-500 border-indigo-500': openTemplate === {{ $tpl['id'] }} }">
                        
                        <!-- Accordion Header -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50">
                            <button @click="openTemplate = openTemplate === {{ $tpl['id'] }} ? null : {{ $tpl['id'] }}" 
                                class="flex-1 flex items-center gap-3 text-left">
                                <span class="font-bold text-gray-900 dark:text-gray-100 tracking-tight">{{ $tpl['name'] }}</span>
                                @if($tpl['is_active'])
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400 rounded-full uppercase">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-full uppercase">Nonaktif</span>
                                @endif
                                @if($tpl['description'])
                                    <span class="hidden sm:inline text-xs text-gray-500 dark:text-gray-400 font-normal ml-2">({{ $tpl['description'] }})</span>
                                @endif
                            </button>
                            
                            <div class="flex items-center gap-1">
                                <button wire:click="toggleTemplateActive({{ $tpl['id'] }})"
                                    class="p-2 rounded-lg transition-colors {{ $tpl['is_active'] ? 'text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20' : 'text-gray-400 dark:text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                                    title="{{ $tpl['is_active'] ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                                <button wire:click="editTemplate({{ $tpl['id'] }})"
                                    class="p-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="triggerConfirm('deleteTemplate', {{ $tpl['id'] }}, 'Hapus Template?', 'Apakah Anda yakin ingin menghapus template ini?')"
                                    class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <button @click="openTemplate = openTemplate === {{ $tpl['id'] }} ? null : {{ $tpl['id'] }}"
                                    class="p-2 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-transform duration-300"
                                    :class="{ 'rotate-180': openTemplate === {{ $tpl['id'] }} }">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Accordion Content -->
                        <div x-show="openTemplate === {{ $tpl['id'] }}" x-collapse x-cloak
                            class="border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50">
                            <div class="p-5">
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 font-mono text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap border border-gray-100 dark:border-gray-700/50">{{ $tpl['content'] }}</div>
                                
                                @if(isset($tpl['variables']) && count($tpl['variables']) > 0)
                                    <div class="mt-4">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Variabel Tersedia:</span>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($tpl['variables'] as $var)
                                                <code class="px-2 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-md text-xs font-bold">{!! '{'.$var.'}' !!}</code>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
             <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p>Belum ada template pesan</p>
            </div>
        @endif
    </div>

    <!-- Template Modal -->
    <div x-show="$wire.showTemplateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="$wire.showTemplateModal = false; $wire.closeTemplateModal()">
                    <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveTemplate">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                {{ $editTemplateId ? 'Edit Template' : 'Tambah Template' }}
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama (Slug) *</label>
                                    <input type="text" wire:model="template_name"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="billing-reminder">
                                    @error('template_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                                    <input type="text" wire:model="template_description"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Konten Pesan *</label>
                                    <textarea wire:model="template_content" rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        placeholder="Halo {name}, tagihan Anda sebesar {amount}..."></textarea>
                                    @error('template_content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Gunakan {variable} untuk data dinamis.</p>
                                </div>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="template_is_active"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                                </label>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                Simpan
                            </button>
                            <button type="button" wire:click="closeTemplateModal"
                                class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
