<x-simple-layout>
    @section("head")
        <style>
            a.border {
                transition: box-shadow 300ms ease;
            }
            a.border:hover {
                box-shadow: 0 .5rem 1rem rgba(34,41,47,.15)!important;
            }
        </style>
    @endsection
    @include("errors.common.fillable", $viewContent)
</x-simple-layout>
