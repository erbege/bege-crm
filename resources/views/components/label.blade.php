@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-black text-[10px] uppercase tracking-widest text-gray-700 dark:text-gray-400 mb-1']) }}>
    {{ $value ?? $slot }}
</label>