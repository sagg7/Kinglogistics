<x-app-layout>
    <x-slot name="crumb_section">Rate</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/rates/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            (() => {
                $("#shipper_id")
                    .html(`<option value="{{ $rate->shipper_id }}">{{ $rate->shipper->name }}</option>`)
                    .val({{ $rate->shipper_id }})
                    .trigger('change');
                $("#zone_id")
                    .html(`<option value="{{ $rate->zone_id }}">{{ $rate->zone->name }}</option>`)
                    .val({{ $rate->zone_id }})
                    .trigger('change');
            })();
        </script>
    @endsection

    @section('modals')
        @include('rates.common.modals.addRateGroup')
        @include('rates.common.modals.deleteRateGroup')
    @endsection

    {!! Form::open(['route' => ['rate.update', $rate->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('rates.common.form')
    {!! Form::close() !!}
</x-app-layout>
