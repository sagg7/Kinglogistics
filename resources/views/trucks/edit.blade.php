<x-app-layout>
    <x-slot name="crumb_section">Truck</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/sections/trucks/common.min.js?1.0.0') }}"></script>
        <script src="{{ asset('js/common/filesUploads.min.js?1.0.1') }}"></script>
        <script>
            (() => {
                @if($truck->trailer)
                $("#trailer_id")
                    .html(`<option value="{{ $truck->trailer_id }}">{{ $truck->trailer->number }}</option>`)
                    .val({{ $truck->trailer_id }})
                    .trigger('change');
                @endif
                @if($truck->carrier)
                $("#carrier_id")
                    .html(`<option value="{{ $truck->carrier_id }}">{{ $truck->carrier->name }}</option>`)
                    .val({{ $truck->carrier_id }})
                    .trigger('change');
                @endif
                @if($truck->seller)
                $("#seller_id")
                    .html(`<option value="{{ $truck->seller_id }}">{{ $truck->seller->name }}</option>`)
                    .val({{ $truck->seller_id }})
                    .trigger('change');
                @endif
            })();
        </script>
    @endsection

    @component('components.nav-pills-form', ['pills' => [['name' => 'General', 'icon' => 'fas fa-user-circle', 'pane' => 'pane-general'],['name' => 'Paperwork', 'icon' => 'fas fa-folder-open', 'pane' => 'pane-paperwork']]])
        <div role="tabpanel" class="tab-pane active" id="pane-general" aria-labelledby="pane-general"
             aria-expanded="true">
            {!! Form::open(['route' => ['truck.update', $truck->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('trucks.common.form')
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-paperwork" aria-labelledby="pane-paperwork"
             aria-expanded="true">
            @include('common.paperwork.filesTemplates', ['related_id' => $truck->id])
            <hr>
            {!! Form::open(['route' => ['paperwork.storeFiles'], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('common.paperwork.filesUploads', ['related_id' => $truck->id, 'type' => 'truck'])
            {!! Form::close() !!}
        </div>
    @endcomponent
</x-app-layout>
