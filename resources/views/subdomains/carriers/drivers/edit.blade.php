<x-app-layout>
    <x-slot name="crumb_section">Driver</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section("scripts")
        <script src="{{ asset('js/sections/subdomains/carriers/drivers/common.min.js') }}"></script>
        <script>
            (() => {
                $("#truck_id")
                    .html(`<option value="{{ $driver->truck_id }}">{{ $driver->truck->number }}</option>`)
                    .val({{ $driver->truck_id }})
                    .trigger('change');
                $("#trailer_id")
                    .html(`<option value="{{ $driver->trailer_id }}">{{ $driver->trailer->number }}</option>`)
                    .val({{ $driver->trailer_id }})
                    .trigger('change');
            })();
        </script>
    @endsection

    {!! Form::open(['route' => ['driver.update', $driver->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.drivers.common.form')
    {!! Form::close() !!}
</x-app-layout>
