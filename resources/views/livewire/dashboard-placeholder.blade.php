<div>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Premium Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Customer Stats Placeholder -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-pulse">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                        <div class="w-16 h-6 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                    <div class="flex items-baseline space-x-2">
                        <div class="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-4 w-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                    <div
                        class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex justify-between items-center">
                        <div class="h-3 w-10 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-3 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                </div>

                <!-- Invoice Stats Placeholder -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-pulse">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                        <div class="w-16 h-6 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                    <div class="flex items-baseline space-x-2">
                        <div class="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-4 w-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex space-x-4">
                        <div class="flex flex-col gap-1">
                            <div class="h-3 w-10 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            <div class="h-3 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <div class="h-3 w-10 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            <div class="h-3 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Stats Placeholder -->
                <div class="bg-indigo-600 dark:bg-indigo-600 rounded-xl shadow-lg p-6 animate-pulse">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-lg bg-white/20"></div>
                        <div class="w-16 h-6 rounded-lg bg-white/20"></div>
                    </div>
                    <div class="h-4 w-24 bg-white/20 rounded mb-2"></div>
                    <div class="flex flex-col gap-2">
                        <div class="h-8 w-32 bg-white/20 rounded"></div>
                        <div class="h-3 w-24 bg-white/20 rounded"></div>
                    </div>
                </div>

                <!-- Hotspot Stats Placeholder -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 animate-pulse">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                        <div class="w-16 h-6 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
                    </div>
                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                    <div class="flex items-baseline space-x-2">
                        <div class="h-8 w-16 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-4 w-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                    <div
                        class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700/50 flex justify-between items-center text-xs">
                        <div class="flex items-center space-x-1">
                            <div class="h-2 w-2 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                            <div class="h-3 w-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        </div>
                        <div class="flex items-center space-x-1">
                            <div class="h-2 w-2 rounded-full bg-gray-200 dark:bg-gray-700"></div>
                            <div class="h-3 w-12 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Invoices Table Placeholder -->
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl border border-gray-100 dark:border-gray-700/50 animate-pulse">
                <div
                    class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
                    <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="h-3 w-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
                <div class="overflow-x-auto">
                    <div class="p-6 space-y-4">
                        @for ($i = 0; $i < 5; $i++)
                            <div class="flex justify-between items-center">
                                <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                <div class="h-6 w-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>