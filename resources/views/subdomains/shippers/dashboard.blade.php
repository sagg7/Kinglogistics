<x-app-layout>

    @section("scripts")
        <script>
            const guard = 'shipper';
        </script>
        <script src="{{ asset('js/sections/dashboard/common.min.js') }}"></script>
    @endsection

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoadStatus", "title" => "Load Status"])
    @endsection

    @include('dashboard.common.loadStatus')

</x-app-layout>
