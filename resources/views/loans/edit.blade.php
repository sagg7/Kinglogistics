<x-app-layout>
    <x-slot name="crumb_section">Loan</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/loans/common.min.js') }}"></script>
        <script>
            (() => {
                $("#carrier")
                    .html(`<option value="{{ $loan->carrier_id }}">{{ $loan->carrier->name }}</option>`)
                    .val({{ $loan->carrier_id }})
                    .trigger('change');
            })();
        </script>
    @endsection

    {!! Form::open(['route' => ['loan.update', $loan->id], 'method' => 'post', 'class' => 'form form-vertical','enctype' => 'multipart/form-data']) !!}
    @include('loans.common.form')
    {!! Form::close() !!}
</x-app-layout>
