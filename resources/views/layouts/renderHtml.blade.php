<x-app-layout>
    <x-slot name="crumb_section">{{ $section }}</x-slot>

    <div class="card">
        <div class="card-body">
            <h1>{{ $title }}</h1>
            {!! $html !!}
        </div>
    </div>
</x-app-layout>
