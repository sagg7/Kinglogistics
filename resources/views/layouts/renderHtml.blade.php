<x-app-layout>
    <x-slot name="crumb_section">{{ $section }}</x-slot>

    <div class="card">
        <div class="card-body">
            @isset($html)
                <h1>{{ $title }}</h1>
                {!! $html !!}
            @else
                <div class="text-center">
                    <h3>No information has been provided at this time</h3>
                </div>
            @endisset
        </div>
    </div>
</x-app-layout>
