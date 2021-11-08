<x-app-layout>
    <x-slot name="crumb_section">Paperwork</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section("head")
        <style>
            code {
                font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
            }
        </style>
    @endsection

    @section("scripts")
        <script src="{{ asset('js/sections/paperwork/common.min.js?1.0.0') }}"></script>
    @endsection

    @section('modals')
        @include('paperwork.common.modals.infoModal')
    @endsection
    <div class="tab-content">
        <div class="tab-pane active" id="simple" aria-labelledby="simple-tab" role="tabpanel">
            {!! Form::open(['route' => ['paperwork.store'], 'method' => 'post', 'class' => 'form form-vertical', 'enctype' => 'multipart/form-data']) !!}
            @include('paperwork.common.form')
            {!! Form::close() !!}
        </div>
    </div>
</x-app-layout>
