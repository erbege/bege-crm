<div class="flex flex-col items-center">
    <div class="mb-6">
        {{ $logo }}
    </div>

    <div
        class="w-full sm:max-w-md mt-2 px-8 py-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl shadow-2xl overflow-hidden sm:rounded-2xl border border-gray-100 dark:border-gray-700/50 ring-1 ring-black/5">
        {{ $slot }}
    </div>
</div>