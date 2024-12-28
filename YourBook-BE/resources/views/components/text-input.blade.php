@props(['disabled' => false, 'name'])

<input {{ $disabled ? 'disabled' : '' }} name="{{$name}}"  {!! $attributes->merge(['class' => 'input ']) !!}>
<x-input-error :messages="$errors->get($name)" class="mt-2" />
