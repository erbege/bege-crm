@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700/50 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 rounded-lg shadow-sm transition-all duration-300']) !!}>