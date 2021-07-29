<x-app-layout>
    <x-slot name="crumb_section">Messages</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('head')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endsection

    @section('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script>
            const content = @json($message->message_json);
            (() => {
                $("#carrier_id")
                    .html(`<option value="{{ $message->carrier_id }}">{{ $message->carrier->name }}</option>`)
                    .val({{ $message->carrier_id }})
                    .trigger('change');
                $("#zone_id")
                    .html(`<option value="{{ $message->zone_id }}">{{ $message->zone->name }}</option>`)
                    .val({{ $message->zone_id }})
                    .trigger('change');
                const drivers = @json($message->drivers);
                let driversHtml = '',
                    driversIds = [];
                drivers.forEach((item) => {
                    driversHtml += `<option value="${item.id}">${item.name}</option>`;
                    driversIds.push(item.id);
                });
                $('[name="drivers[]"]')
                    .html(driversHtml)
                    .val(driversIds)
                    .trigger('change');
            })();
        </script>
        <script src="{{ asset("js/sections/safetyMessages/common.min.js") }}"></script>
    @endsection

    {!! Form::open(['route' => ['safetyMessage.update', $message->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('safetyMessages.common.form')
    {!! Form::close() !!}
</x-app-layout>
