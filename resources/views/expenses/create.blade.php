<x-app-layout>
    <x-slot name="crumb_section">Expense</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script>
            (() => {
                const typeSel = $('#type'),
                    truckSel = $('#truck_id');
                typeSel.select2({
                    placeholder: 'Select',
                });

                truckSel.select2({
                    ajax: {
                        url: '/truck/selection',
                        data: (params) => {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                take: 15,
                            };
                        },
                    },
                    placeholder: 'Select',
                    allowClear: true,
                });
            })();
        </script>
    @endsection

    {!! Form::open(['route' => 'expense.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('subdomains.carriers.expenses.common.form')
    {!! Form::close() !!}
</x-app-layout>
