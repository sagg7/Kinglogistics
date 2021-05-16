<x-app-layout>
    <x-slot name="crumb_section">Driver</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script>
            (() => {
                $('#truck_id').select2({
                    ajax: {
                        url: '/truck/selection',
                        data: (params) => {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                take: 15,
                            };
                        },
                    }
                });
                $('#trailer_id').select2({
                    ajax: {
                        url: '/trailer/selection',
                        data: (params) => {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                take: 15,
                            };
                        },
                    }
                });
            })();
        </script>
    @endsection

    {!! Form::open(['route' => 'driver.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('drivers.common.form')
    {!! Form::close() !!}
</x-app-layout>
