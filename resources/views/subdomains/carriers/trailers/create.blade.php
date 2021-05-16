<x-app-layout>
    <x-slot name="crumb_section">Trailer</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/subdomains/carriers/trailers/common.min.js') }}"></script>
    @endsection

    {!! Form::open(['route' => 'trailer.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.trailers.common.form')
    {!! Form::close() !!}
</x-app-layout>
