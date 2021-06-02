<x-app-layout>
    <x-slot name="crumb_section">Driver</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section("scripts")
        <script src="{{ asset('js/sections/subdomains/carriers/drivers/common.min.js') }}"></script>
        <script src="{{ asset('js/common/filesUploads.min.js') }}"></script>
        <script>
            (() => {
                $("#zone_id")
                    .html(`<option value="{{ $driver->zone_id }}">{{ $driver->zone->name }}</option>`)
                    .val({{ $driver->zone_id }})
                    .trigger('change');
            })();
        </script>
    @endsection

    @component('components.nav-pills-form', ['pills' => [['name' => 'General', 'icon' => 'fas fa-user-circle', 'pane' => 'pane-general'],['name' => 'Paperwork', 'icon' => 'fas fa-folder-open', 'pane' => 'pane-paperwork']]])
        <div role="tabpanel" class="tab-pane active" id="pane-general" aria-labelledby="pane-general"
             aria-expanded="true">
            {!! Form::open(['route' => ['driver.update', $driver->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('subdomains.carriers.drivers.common.form')
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-paperwork" aria-labelledby="pane-paperwork"
             aria-expanded="true">
            {!! Form::open(['route' => ['paperwork.storeFiles'], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('common.filesUploads', ['related_id' => $driver->id, 'type' => 'driver'])
            {!! Form::close() !!}
        </div>
    @endcomponent
</x-app-layout>
