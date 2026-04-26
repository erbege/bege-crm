@php
  $logo = \App\Models\Setting::get('general.company_logo');
@endphp

@if($logo)
  <img src="{{ Storage::url($logo) }}" {{ $attributes->merge(['class' => 'h-9 w-auto']) }}>
@else
  <img src="{{ asset('images/logo.png') }}" {{ $attributes->merge(['class' => 'h-9 w-auto']) }}>
@endif