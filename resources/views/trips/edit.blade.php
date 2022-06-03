<x-app-layout>
    <x-slot name="crumb_section">{{session('renames') ? session('renames')->job : 'Job'}}</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script>
            (() => {
                $("#shipper_id")
                    .html(`<option value="{{ $trip->shipper_id }}">{{ $trip->shipper->name }}</option>`)
                    .val({{ $trip->shipper_id }})
                    .trigger('change');
                @if($trip->rate_id)
                $("#rate_id")
                    .html(`<option value="{{ $trip->rate_id }}">{{ $trip->rate->rate_group->name }}: {{ $trip->rate->start_mileage }} - {{ $trip->rate->end_mileage }} miles</option>`)
                    .val({{ $trip->rate_id }})
                    .trigger('change');
                @endif
            })();
        </script>
        <script src="{{ asset('js/sections/trips/common.min.js?1.0.2') }}"></script>
    @endsection

    {!! Form::open(['route' => ['trip.update', $trip->id], 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'loadForm']) !!}
    @include('trips.common.form')
    {!! Form::close() !!}
</x-app-layout>
