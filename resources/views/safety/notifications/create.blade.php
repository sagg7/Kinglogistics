<x-app-layout>
    <x-slot name="crumb_section">Notification</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    @section('head')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endsection

    @section('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script>
            /*(() => {
                const toolbarOptions = [
                    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video'],

                    [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                    [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                    [{ 'direction': 'rtl' }],                         // text direction

                    [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                    //[{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                    //[{ 'font': [] }],
                    [{ 'align': [] }],

                    ['link', 'image']

                    ['clean']                                         // remove formatting button
                ];
                const quill = new Quill('#message', {
                    modules: { toolbar: toolbarOptions },
                    theme: 'snow'
                });

                $('form').submit((e) => {
                    e.preventDefault();
                    console.log(quill.getText());
                    console.log(quill.root.innerHTML.trim());
                });
            })();*/
        </script>
    @endsection

    {!! Form::open(['route' => 'notification.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('notifications.common.form')
    {!! Form::close() !!}
</x-app-layout>
