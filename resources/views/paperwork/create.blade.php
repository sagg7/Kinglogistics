<x-app-layout>
    <x-slot name="crumb_section">Paperwork</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section("scripts")
        <script src="{{ asset('js/sections/paperwork/common.min.js') }}"></script>
    @endsection

    <div class="tab-content">
        <div class="tab-pane active" id="simple" aria-labelledby="simple-tab" role="tabpanel">
            {!! Form::open(['route' => ['paperwork.store'], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('paperwork.common.form')
            {!! Form::close() !!}
        </div>
    </div>
</x-app-layout>
