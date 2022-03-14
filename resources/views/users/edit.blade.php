<x-app-layout>
    <x-slot name="crumb_section">Staff</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
        <script src="{{ asset('js/common/filesUploads.min.js?1.0.1') }}"></script>
    @endsection

    @component('components.nav-pills-form', ['pills' => [
       ['name' => 'General', 'icon' => 'fas fa-user-circle', 'pane' => 'pane-general'],
       ['name' => 'Paperwork', 'icon' => 'fas fa-folder-open', 'pane' => 'pane-paperwork'],
       ]])
        <div role="tabpanel" class="tab-pane active" id="pane-general" aria-labelledby="pane-general" aria-expanded="true">
            {!! Form::open(['route' => ['user.update', $user->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
            @include('users.common.form')
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-paperwork" aria-labelledby="pane-paperwork"
             aria-expanded="true">
            @include('common.paperwork.filesTemplates', ['related_id' => $user->id])
            <hr>
            {!! Form::open(['route' => ['paperwork.storeFiles'], 'method' => 'post', 'class' => 'form form-vertical', 'enctype' => 'multipart/form-data']) !!}
            @include('common.paperwork.filesUploads', ['related_id' => $user->id, 'type' => 'staff'])
            {!! Form::close() !!}
        </div>
    @endcomponent
</x-app-layout>
