<x-app-layout>
    <x-slot name="crumb_section">expense</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section("scripts")
        @if($expense->truck_id)
        <script>
            (() => {

                $("#truck_id")
                    .html(`<option value="{{ $expense->truck_id }}">{{ $expense->truck->number }}</option>`)
                    .val({{ $expense->truck_id }})
                    .trigger('change');
            })();
        </script>
        @endif
    @endsection

    {!! Form::open(['route' => ['expense.update', $expense->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.expenses.common.form')
    {!! Form::close() !!}
</x-app-layout>
