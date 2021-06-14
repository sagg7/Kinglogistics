<x-app-layout>
    <x-slot name="crumb_section">Profile</x-slot>

    @section("scripts")
        <script src="{{ asset('js/common/filesUploads.min.js') }}"></script>
    @endsection

    @component('components.nav-pills-form', ['pills' => [['name' => 'General', 'icon' => 'fas fa-user-circle', 'pane' => 'pane-general'],['name' => 'Paperwork', 'icon' => 'fas fa-folder-open', 'pane' => 'pane-paperwork']]])
        <div role="tabpanel" class="tab-pane active" id="pane-general" aria-labelledby="pane-general"
             aria-expanded="true">
            {!! Form::open(['route' => ['profile.update', $carrier->id, 1], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('carriers.common.form')
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-paperwork" aria-labelledby="pane-paperwork"
             aria-expanded="true">
            @include('common.paperwork.filesTemplates', ['related_id' => $carrier->id])
            <hr>
            {!! Form::open(['route' => ['paperwork.storeFiles'], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('common.paperwork.filesUploads', ['related_id' => $carrier->id, 'type' => 'carrier'])
            {!! Form::close() !!}
        </div>
    @endcomponent
</x-app-layout>
