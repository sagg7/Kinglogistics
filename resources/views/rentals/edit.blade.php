<x-app-layout>
    <x-slot name="crumb_section">Rental</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section("scripts")
        <script src="{{ asset('js/sections/rentals/common.min.js') }}"></script>
        <script>
            (() => {
                $("#carrier_id")
                    .html(`<option value="{{ $rental->carrier_id }}">{{ $rental->carrier->name }}</option>`)
                    .val({{ $rental->carrier_id }})
                    .trigger('change');
                $("#driver_id")
                    .html(`<option value="{{ $rental->driver_id }}">{{ $rental->driver->name }}</option>`)
                    .val({{ $rental->driver_id }})
                    .trigger('change');
                $("#trailer_id")
                    .html(`<option value="{{ $rental->trailer_id }}">{{ $rental->trailer->number }}</option>`)
                    .val({{ $rental->trailer_id }})
                    .trigger('change');
            })();
        </script>
    @endsection

    {!! Form::open(['route' => ['rental.update', $rental->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('rentals.common.form')
    {!! Form::close() !!}
</x-app-layout>
