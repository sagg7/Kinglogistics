<x-app-layout>

    @section("scripts")
        <script>
            const guard = 'web';
        </script>
        <script src="{{ asset('js/modules/laravel-echo/echo.js') }}"></script>
        <script src="{{ asset('js/sections/dashboard/common.min.js?1.0.5') }}"></script>
    @endsection

    @section('modals')
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoadStatus", "title" => "Load Status"])
        @include("common.modals.genericAjaxLoading", ["id" => "viewLoad", "title" => "Load"])
    @endsection

    @include('dashboard.common.loadStatus')

</x-app-layout>
