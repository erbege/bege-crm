<x-guest-layout>
    <div class="w-full max-w-md animate-fade-in relative z-20">
        <div class="text-center mb-8">
            <x-authentication-card-logo class="mx-auto drop-shadow-lg" />
            <h1 class="mt-6 text-3xl font-black text-gray-900 dark:text-white tracking-tight">Welcome Back</h1>
            <p class="mt-2 text-sm font-medium text-gray-500 dark:text-gray-400">Please enter your details to sign in
            </p>
        </div>

        <x-authentication-card>
            <x-slot name="logo">
                {{-- Logo moved to header for better impact --}}
            </x-slot>

            <x-validation-errors class="mb-6" />

            @session('status')
                <div
                    class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800/30 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ $value }}
                </div>
            @endsession

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <x-label for="email" value="{{ __('Email Address') }}" />
                    <x-input id="email" class="block mt-1 w-full h-12 px-4 shadow-sm" type="email" name="email"
                        :value="old('email')" required autofocus autocomplete="username"
                        placeholder="name@company.com" />
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <x-label for="password" value="{{ __('Password') }}" />
                        @if (Route::has('password.request'))
                            <a class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot?') }}
                            </a>
                        @endif
                    </div>
                    <x-input id="password" class="block mt-1 w-full h-12 px-4 shadow-sm" type="password" name="password"
                        required autocomplete="current-password" placeholder="••••••••" />
                </div>

                <div class="flex items-center">
                    <label for="remember_me" class="flex items-center cursor-pointer group">
                        <x-checkbox id="remember_me" name="remember"
                            class="w-5 h-5 transition-all text-indigo-600 focus:ring-indigo-500" />
                        <span
                            class="ms-3 text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">{{ __('Keep me signed in') }}</span>
                    </label>
                </div>

                <div class="pt-2">
                    <x-button class="w-full h-12 flex justify-center text-sm">
                        {{ __('Sign In') }}
                    </x-button>
                </div>
            </form>
        </x-authentication-card>

        @if (Route::has('register'))
            <p class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
                Don't have an account?
                <a href="{{ route('register') }}"
                    class="font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 transition-colors">
                    Contact Administrator
                </a>
            </p>
        @endif

        <div class="mt-12 text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                &copy; {{ date('Y') }} {{ config('app.name') }} &bull; All Rights Reserved
            </p>
        </div>
    </div>
</x-guest-layout>