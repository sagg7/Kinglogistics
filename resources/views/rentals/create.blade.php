<x-app-layout>
    <x-slot name="crumb_section">Rental</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('scripts')
        <script>
            (() => {
                $('#carrier_id').select2({
                    ajax: {
                        url: '/carrier/selection',
                        data: (params) => {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                take: 15,
                            };
                        },
                    }
                });
                $('#driver_id').select2({
                    ajax: {
                        url: '/driver/selection',
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

    {!! Form::open(['route' => 'rental.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('rentals.common.form')
    {!! Form::close() !!}
</x-app-layout>
