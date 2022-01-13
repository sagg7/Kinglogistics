<x-app-layout>
    <x-slot name="crumb_section">Driver</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section("scripts")
        <script src="{{ asset('js/sections/drivers/common.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/common/filesUploads.min.js?1.0.1') }}"></script>
        <script>
            (() => {
                const shippers = @json($driver->shippers);
                let shippersHtml = '',
                    shippersIds = [];
                shippers.forEach((item) => {
                    shippersHtml += `<option value="${item.id}">${item.name}</option>`;
                    shippersIds.push(item.id);
                });
                $('[name="shippers[]"]')
                    .html(shippersHtml)
                    .val(shippersIds)
                    .trigger('change');
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
            @include('drivers.common.form')
            {!! Form::close() !!}
        </div>
        @if(auth()->user()->can(['read-paperwork']))
            <div role="tabpanel" class="tab-pane" id="pane-paperwork" aria-labelledby="pane-paperwork"
                 aria-expanded="true">
                @include('common.paperwork.filesTemplates', ['related_id' => $driver->id])
                <hr>
                {!! Form::open(['route' => ['paperwork.storeFiles'], 'method' => 'post', 'class' => 'form form-vertical']) !!}
                @include('common.paperwork.filesUploads', ['related_id' => $driver->id, 'type' => 'driver'])
                {!! Form::close() !!}
            </div>
        @endif

    @endcomponent
</x-app-layout>
