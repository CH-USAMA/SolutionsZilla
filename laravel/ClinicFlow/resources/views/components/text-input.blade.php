@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-400 bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!}>