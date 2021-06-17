<x-app-layout>
    <x-slot name="crumb_section">Notification</x-slot>
    <x-slot name="crumb_subsection">Edit</x-slot>

    @section('head')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endsection

    @section('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script>
            const content = @json($notification->message_json);
            (() => {
                $("#carrier_id")
                    .html(`<option value="{{ $notification->carrier_id }}">{{ $notification->carrier->name }}</option>`)
                    .val({{ $notification->carrier_id }})
                    .trigger('change');
                $("#zone_id")
                    .html(`<option value="{{ $notification->zone_id }}">{{ $notification->zone->name }}</option>`)
                    .val({{ $notification->zone_id }})
                    .trigger('change');
                const drivers = @json($notification->drivers);
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
        <script src="{{ asset("js/sections/notifications/common.min.js") }}"></script>
    @endsection

    {!! Form::open(['route' => ['notification.update', $notification->id], 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('notifications.common.form')
    {!! Form::close() !!}
</x-app-layout>
