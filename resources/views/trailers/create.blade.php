<x-app-layout>
    <x-slot name="crumb_section">Trailer</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/trailers/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
    @endsection

    @section('modals')
        @include('trailers.common.modals.addTrailerType')
        @include('trailers.common.modals.deleteTrailerType')
    @endsection

    {!! Form::open(['route' => 'trailer.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('trailers.common.form')
    {!! Form::close() !!}
</x-app-layout>
