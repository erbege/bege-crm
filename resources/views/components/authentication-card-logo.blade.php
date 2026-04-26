@php
    $logo = \App\Models\Setting::get('general.company_logo');
@endphp

@if($logo)
    <a href="/">
        <img src="{{ Storage::url($logo) }}" {{ $attributes->merge(['class' => 'h-16 w-auto']) }}>
    </a>
@else
    <a href="/">
        <img src="{{ asset('images/logo.png') }}" {{ $attributes->merge(['class' => 'h-16 w-auto']) }}>
    </a>
@endif