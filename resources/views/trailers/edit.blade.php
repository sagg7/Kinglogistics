<x-app-layout>
    <x-slot name="crumb_section">Trailer</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('modals')
        @include('trailers.common.modals.addTrailerType')
        @include('trailers.common.modals.deleteTrailerType')
    @endsection

    @section('scripts')
        <script src="{{ asset('js/sections/trailers/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            (() => {
                $("#shipper_id")
                    .html(`<option value="{{ $trailer->shipper_id }}">{{ $trailer->shipper->name }}</option>`)
                    .val({{ $trailer->shipper_id }})
                    .trigger('change');
            })();
        </script>
    @endsection

    {!! Form::open(['route' => ['trailer.update', $trailer->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('trailers.common.form')
    {!! Form::close() !!}
</x-app-layout>
