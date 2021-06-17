<x-app-layout>
    <x-slot name="crumb_section">Job Opportunity</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('head')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endsection

    @section('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script>
            const content = @json($opportunity->message_json);
            (() => {
                const carriers = @json($opportunity->carriers);
                let carriersHtml = '',
                    carriersIds = [];
                carriers.forEach((item) => {
                    carriersHtml += `<option value="${item.id}">${item.name}</option>`;
                    carriersIds.push(item.id);
                });
                $('[name="carriers[]"]')
                    .html(carriersHtml)
                    .val(carriersIds)
                    .trigger('change');
            })();
        </script>
        <script src="{{ asset("js/sections/jobOpportunities/common.min.js") }}"></script>
    @endsection

    {!! Form::open(['route' => ['jobOpportunity.update', $opportunity->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('jobOpportunities.common.form')
    {!! Form::close() !!}
</x-app-layout>
