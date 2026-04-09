<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Provisioning Script') }} - {{ $subscription->customer->name ?? 'Unknown Customer' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Generated Script ({{ ucfirst($subscription->olt?->brand ?? 'Unknown') }})
                </h3>

                <div class="mb-4">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        OLT: {{ $subscription->olt?->name }} ({{ $subscription->olt?->ip_address }}) <br>
                        Interface:
                        {{ $subscription->olt_frame }}/{{ $subscription->olt_slot }}/{{ $subscription->olt_port }} :
                        {{ $subscription->olt_onu_id }}
                    </p>
                </div>

                <div class="relative">
                    <textarea readonly rows="15"
                        class="w-full font-mono text-sm bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $script }}</textarea>

                    <div class="mt-4 flex gap-4">
                        <button
                            onclick="navigator.clipboard.writeText('{{ str_replace(["\r", "\n"], ["\\r", "\\n"], addslashes($script)) }}'); alert('Copied!');"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Copy to Clipboard
                        </button>

                        <form action="{{ route('provisioning.push', $subscription->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to push this script to the OLT directly via SSH?');">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Push via SSH
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>