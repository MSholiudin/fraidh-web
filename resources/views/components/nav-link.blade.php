@props(['active'])

@php
$classes = ($active ?? false)
            ? 'nav-link-custom active inline-flex items-center px-1 pt-1 border-b-2 border-blue-800 text-sm font-medium leading-5 text-blue-800 focus:outline-none focus:border-blue-900 transition duration-150 ease-in-out'
            : 'nav-link-custom inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-blue-700 hover:text-blue-800 hover:border-blue-800 focus:outline-none focus:text-blue-800 focus:border-blue-800 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>