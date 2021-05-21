<x-app-layout>
    <x-slot name="crumb_section">Driver</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section("scripts")
        <script src="{{ asset('js/sections/subdomains/carriers/drivers/common.min.js') }}"></script>
        <script>
            (() => {
                $("#zone_id")
                    .html(`<option value="{{ $driver->zone_id }}">{{ $driver->zone->name }}</option>`)
                    .val({{ $driver->zone_id }})
                    .trigger('change');
            })();
        </script>
    @endsection

    {!! Form::open(['route' => ['driver.update', $driver->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.drivers.common.form')
    {!! Form::close() !!}
</x-app-layout>
