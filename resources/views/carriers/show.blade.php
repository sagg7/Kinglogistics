<x-app-layout>
    <x-slot name="crumb_section">Carrier</x-slot>
    <x-slot name="crumb_subsection">Summary</x-slot>

    @section("vendorCSS")
        @include("layouts.ag-grid.css")
    @endsection
    @section("scripts")
        @include("layouts.ag-grid.js")
        <script src="{{ asset('js/modules/aggrid/simpleTable.min.js?1.0.0') }}"></script>
        <script>
            const carrier_id = Number({{ $carrier->id }});
        </script>
        <script src="{{ asset('js/sections/carriers/show.min.js?1.0.0') }}"></script>
    @endsection

    <div class="card border border-2 border-primary">
        <div class="card-content">
            <div class="card-header align-items-center">
                <div class="avatar bg-rgba-primary p-50 m-0 mb-1">
                    <div class="avatar-content">
                        <i class="fas fa-dolly-flatbed font-size-large text-primary"></i>
                    </div>
                </div>
                <div class="col">
                    <h1>{{ $carrier->name }}</h1>
                </div>
            </div>
        </div>
    </div>
    @include('carriers.common.carrierCards.income&Ranking')
    @include('carriers.common.carrierCards.alerts&Loads')
    @include('carriers.common.carrierCards.tables&Incidents')
</x-app-layout>
