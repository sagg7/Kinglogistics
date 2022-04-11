<x-app-layout>
    <x-slot name="crumb_section">Carrier</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>
    @section("modals")
    @include("carriers.common.modals.selectTruckModal")
    @include("carriers.common.modals.optionToSelectTruck")
    @endsection
    @section("scripts")
    <script src="{{ asset('js/sections/carriers/selectTruckModal.min.js') }}"></script>
    <script src="{{ asset('js/sections/trucks/commonWithoutCarrier.min.js') }}"></script>

    <script defer>
    let carrierId = null;
    let carrierName = null;
   (() => {
        $('#carrierForm').submit(e => {
                    e.preventDefault();
                    const form = $(e.currentTarget);
                    const formData = new FormData(form[0]);
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: (res) => {
                            if (res.success){
                                carrierId = res.data.id
                                carrierName = res.data.name
                             $("#optionToSelectTruck").modal('show');
                            }

                        },
                        error: () => {
                            throwErrorMsg();
                        }
                    }).always(() => {
                        removeAjaxLoaders();
                    });
                });
    })();

    </script>
    @endsection 

    {!! Form::open(['route' => 'carrier.store', 'method' => 'post', 'class' => 'form form-vertical', 'id'=> 'carrierForm']) !!}
    @include('carriers.common.form')
    {!! Form::close() !!}

   
</x-app-layout>
