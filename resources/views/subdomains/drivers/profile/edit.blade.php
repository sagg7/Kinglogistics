<x-app-layout>
    <x-slot name="crumb_section">Profile</x-slot>

    @section("scripts")
        <script src="{{ asset('js/sections/subdomains/carriers/drivers/common.min.js') }}"></script>
        <script src="{{ asset('js/common/filesUploads.min.js?1.0.1') }}"></script>
    @endsection

    @component('components.nav-pills-form', ['pills' => [['name' => 'General', 'icon' => 'fas fa-user-circle', 'pane' => 'pane-general'],['name' => 'Paperwork', 'icon' => 'fas fa-folder-open', 'pane' => 'pane-paperwork']]])
        <div role="tabpanel" class="tab-pane active" id="pane-general" aria-labelledby="pane-general"
             aria-expanded="true">
            {!! Form::open(['route' => ['driver.profile.update', $driver->id, 1], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('drivers.common.form')
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-paperwork" aria-labelledby="pane-paperwork"
             aria-expanded="true">
            @include('common.paperwork.filesTemplates', ['related_id' => $driver->id])
            <hr>
            {!! Form::open(['route' => ['paperwork.storeFiles'], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('common.paperwork.filesUploads', ['related_id' => $driver->id, 'type' => 'driver'])
            {!! Form::close() !!}
        </div>
    @endcomponent
</x-app-layout>
