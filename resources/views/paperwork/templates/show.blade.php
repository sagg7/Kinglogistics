<x-app-layout>
    <x-slot name="crumb_section">Paperwork</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>

    <style>
        form {
            white-space: pre-line;
        }
    </style>

    @section("scripts")
        @if(count($data["canvases"]) > 0)
            <script>
                const canvases = [
                    @foreach($data["canvases"] as $canvas)
                    {canvas: document.getElementById('{{ $canvas }}'), required: true},
                    @endforeach
                ];
            </script>
            <script src="{{ asset('js/common/initSignature.min.js?1.0.2') }}"></script>
        @endif
        @if(count($data["validation"]) > 0)
            <script>
                //(() => {
                const toValidate = [
                    @foreach($data["validation"] as $item)
                    $(`[name={{ $item }}]`),
                    @endforeach
                ];
                //})();
            </script>
        @endif
        @if(session('error'))
            <script>
                (() => {
                    throwErrorMsg('{{ session('error')  }}');
                })();
            </script>
        @endif
    @endsection

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                {!! Form::open(['route' => ['paperwork.storeTemplate', 'id' => $id, 'related_id' => $related_id], 'class' => 'with-sig-pad']) !!}
                <h1 class="text-center mb-3">{{ $paperwork->name }}</h1>
                {!! $data["html"] !!}
                {!! Form::button('Submit', ['class' => 'btn btn-primary btn-block', 'type' => 'submit']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</x-app-layout>
