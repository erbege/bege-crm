<div x-data="{ showFormModal: false, showDeleteModal: false, showPreviewModal: false }"
    @open-modal.window="showFormModal = true" @open-delete-modal.window="showDeleteModal = true"
    @open-preview-modal.window="showPreviewModal = true"
    @close-modal.window="showFormModal = false; showDeleteModal = false; showPreviewModal = false;">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Hotspot Voucher Templates') }}
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Kelola templat voucher hotspot Anda') }}</p>
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="space-y-6">
                <!-- Control Bar -->
                <div
                    class="bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700/50 rounded-lg p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                        <div class="flex-1 max-w-md relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 dark:bg-gray-900 border-transparent dark:border-gray-700 focus:bg-white dark:focus:bg-gray-800 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 dark:focus:border-indigo-500 rounded-lg text-sm font-medium transition-all dark:text-gray-200 placeholder-gray-400"
                                placeholder="Cari templat voucher...">
                        </div>

                        <div class="flex items-center gap-3">
                            <button @click="showFormModal = true; $wire.openModal()"
                                class="group relative flex items-center justify-center px-6 py-3.5 bg-indigo-600 text-white rounded-lg font-black uppercase tracking-widest text-[10px] hover:bg-indigo-500 focus:ring-4 focus:ring-indigo-200 transition-all shadow-xl shadow-indigo-100 dark:shadow-none overflow-hidden">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/10 to-white/0 -translate-x-full group-hover:translate-x-full transition-transform duration-700">
                                </div>
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Add New Template</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div
                    class="bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700/50 rounded-lg overflow-hidden">
                    <div
                        class="px-8 py-5 border-b border-gray-50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-900/20 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                Voucher Templates Manager</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span
                                class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-tighter">Blade/HTML
                                Support</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/50">
                            <thead class="bg-gray-50/50 dark:bg-gray-900/40">
                                <tr>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                        Template Name</th>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                        Status</th>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                        Tersimpan Pada</th>
                                    <th
                                        class="px-8 py-4 text-right text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 dark:text-gray-500">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-50 dark:divide-gray-700/60">
                                @forelse ($templates as $template)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/20 transition-all group">
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 rounded-full bg-indigo-500 mr-2.5"></div>
                                                <div
                                                    class="text-sm font-black text-gray-900 dark:text-white tracking-tight">
                                                    {{ $template->name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $template->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300' }}">
                                                {{ $template->is_active ? 'Ready' : 'Draft' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div
                                                class="text-[11px] font-bold text-gray-600 dark:text-gray-400 tracking-tight">
                                                {{ $template->updated_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <button
                                                    @click="showPreviewModal = true; $wire.previewTemplate({{ $template->id }})"
                                                    class="p-2 rounded-xl text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-600 hover:text-white transition-all shadow-sm active:shadow-none"
                                                    title="Pratinjau Layout">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="showFormModal = true; $wire.editTemplate({{ $template->id }})"
                                                    class="p-2 rounded-xl text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-600 hover:text-white transition-all shadow-sm active:shadow-none"
                                                    title="Modifikasi Script">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button
                                                    @click="showDeleteModal = true; $wire.confirmDelete({{ $template->id }})"
                                                    class="p-2 rounded-xl text-rose-600 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-600 hover:text-white transition-all shadow-sm active:shadow-none"
                                                    title="Delete Template">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-8 py-12 text-center text-gray-400 dark:text-gray-500 italic text-sm">
                                            No templates saved yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="px-8 py-4 bg-gray-50/50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700/50">
                        {{ $templates->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div x-show="showFormModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showFormModal = false; $wire.closeModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ $editMode ? 'Edit Template' : 'Create Template' }}
                    </h3>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Name -->
                        <div>
                            <x-label for="name" value="{{ __('Name') }}" />
                            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="name" />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="flex items-center">
                            <x-checkbox id="is_active" wire:model="is_active" />
                            <x-label for="is_active" class="ml-2" value="{{ __('Active') }}" />
                        </div>

                        <!-- Content (HTML/Blade) -->
                        <div>
                            <x-label for="content" value="{{ __('Template Content (Blade/HTML)') }}" />
                            <p class="text-xs text-gray-500 mb-2">Available variables: <code>$vouchers</code> (Loop
                                through
                                this), <code>$voucher->code</code>, <code>$voucher->profile->name</code>, etc.</p>
                            <textarea wire:model="content" rows="15"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm font-mono text-sm"></textarea>
                            <x-input-error for="content" class="mt-2" />
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="save"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ $editMode ? __('Update') : __('Create') }}
                    </button>
                    <button type="button" @click="showFormModal = false; $wire.closeModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showDeleteModal = false; $wire.closeDeleteModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Delete Template') }}
                            </h3>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Are you sure you want to delete this template? This action cannot be undone.') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="deleteTemplate"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Delete') }}
                    </button>
                    <button @click="showDeleteModal = false; $wire.closeDeleteModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="showPreviewModal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto"
        x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showPreviewModal = false; $wire.closePreviewModal()">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Template Preview') }}
                    </h3>

                    <div class="space-y-4">
                        <!-- Controls -->
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model.live="previewMode" value="dummy"
                                        class="form-radio text-indigo-600">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Dummy Data</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model.live="previewMode" value="real"
                                        class="form-radio text-indigo-600">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Real Data (Recent Batch)</span>
                                </label>
                            </div>

                            @if($previewMode === 'real')
                                <div class="w-full sm:w-64">
                                    <select wire:model.live="previewBatchId"
                                        class="block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">Select Batch...</option>
                                        @foreach($batches as $batch)
                                            <option value="{{ $batch->batch_id }}">{{ $batch->created_at }}
                                                ({{ $batch->batch_id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <!-- Preview Area -->
                        <div
                            class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-white min-h-[400px] overflow-auto">
                            <div class="prose max-w-none">
                                {!! $previewHtml !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="showPreviewModal = false; $wire.closePreviewModal()"
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>