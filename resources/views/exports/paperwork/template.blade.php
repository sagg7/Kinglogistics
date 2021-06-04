<x-pdf-layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <main style="white-space: pre-line;">
        {!! $html !!}
    </main>
</x-pdf-layout>
