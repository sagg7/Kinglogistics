<x-app-layout>
    <x-slot name="crumb_section">Truck</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/subdomains/carriers/trucks/common.min.js') }}"></script>
        <script>
            (() => {
                @if($truck->driver_id)
                $("#driver_id")
                    .html(`<option value="{{ $truck->driver_id }}">{{ $truck->driver->name }}</option>`)
                    .val({{ $truck->driver_id }})
                    .trigger('change');
                @endif
                @if($truck->trailer_id)
                $("#trailer_id")
                    .html(`<option value="{{ $truck->trailer_id }}">{{ $truck->trailer->number }}</option>`)
                    .val({{ $truck->trailer_id }})
                    .trigger('change');
                @endif
            })();
        </script>
    @endsection

    {!! Form::open(['route' => ['truck.update', $truck->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.trucks.common.form')
    {!! Form::close() !!}
</x-app-layout>
