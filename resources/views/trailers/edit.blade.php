<x-app-layout>
    <x-slot name="crumb_section">Trailer</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('modals')
        @include('trailers.common.modals.addTrailerType')
        @include('trailers.common.modals.deleteTrailerType')
    @endsection

    @section('scripts')
        <script src="{{ asset('js/sections/trailers/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script src="{{ asset('js/common/filesUploads.min.js') }}"></script>
        <script>
            (() => {
                const shippers = @json($trailer->shippers);
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
            })();
        </script>
    @endsection

    @component('components.nav-pills-form', ['pills' => [['name' => 'General', 'icon' => 'fas fa-user-circle', 'pane' => 'pane-general'],['name' => 'Paperwork', 'icon' => 'fas fa-folder-open', 'pane' => 'pane-paperwork']]])
        <div role="tabpanel" class="tab-pane active" id="pane-general" aria-labelledby="pane-general"
             aria-expanded="true">
            {!! Form::open(['route' => ['trailer.update', $trailer->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('trailers.common.form')
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-paperwork" aria-labelledby="pane-paperwork"
             aria-expanded="true">
            @include('common.paperwork.filesTemplates', ['related_id' => $trailer->id])
            <hr>
            {!! Form::open(['route' => ['paperwork.storeFiles'], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('common.paperwork.filesUploads', ['related_id' => $trailer->id, 'type' => 'trailer'])
            {!! Form::close() !!}
        </div>
    @endcomponent

</x-app-layout>
