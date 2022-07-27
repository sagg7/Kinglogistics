<x-app-layout>
    <x-slot name="crumb_section">Description</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('scripts')
    @endsection

    {!! Form::open(['route' => ['loadDescription.update', $description->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    <div class="card">
        <div class="card-body">
            <div class="card-content">
                @include('loads.descriptions.common.form')
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</x-app-layout>
