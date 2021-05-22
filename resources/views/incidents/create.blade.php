<x-app-layout>
    <x-slot name="crumb_section">Incident</x-slot>
    <x-slot name="crumb_subsection">Create</x-slot>
    <style>
        canvas {
            border: 1px solid #D9D9D9;
        }
    </style>

    @section('scripts')
        <script src="{{ asset('js/sections/incidents/common.min.js') }}"></script>
        <script src="{{ asset('js/common/typesModal.min.js') }}"></script>
        <script>
            const canvases = [
                    document.getElementById('safety_signature'),
                    document.getElementById('driver_signature')
                ],
                resizeCanvas = (canvas, sigPad) => {
                    // When zoomed out to less than 100%, for some very strange reason,
                    // some browsers report devicePixelRatio as less than 1
                    // and only part of the canvas is cleared then.
                    var ratio =  Math.max(window.devicePixelRatio || 1, 1);

                    // This part causes the canvas to be cleared
                    canvas.width = canvas.offsetWidth * ratio;
                    canvas.height = canvas.offsetHeight * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);

                    // This library does not listen for canvas changes, so after the canvas is automatically
                    // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
                    // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
                    // that the state of this library is consistent with visual state of the canvas, you
                    // have to clear it manually.
                    sigPad.clear();
                };
            let sigPadArr = [];
            canvases.forEach((canvas) => {
                const sigPad = new SignaturePad(canvas),
                    btn = canvas.parentNode.querySelector("button"),
                    input = document.createElement("input");

                console.log($(btn));

                btn.addEventListener("click", (e) => {
                    sigPad.clear();
                });

                input.name = canvas.id;
                input.hidden = true;

                canvas.parentNode.insertBefore(input, canvas.nextSibling);

                sigPadArr.push({signaturePad: sigPad, input});
                window.onresize = () => {
                    resizeCanvas(canvas, sigPad);
                };
            });

            $('form').submit((e) => {
                sigPadArr.forEach((obj) => {
                    obj.input.value = obj.signaturePad.toDataURL();
                });
            });
        </script>
    @endsection

    @section('modals')
        @include('incidents.common.modals.addIncidentType')
        @include('incidents.common.modals.deleteIncidentType')
    @endsection

    {!! Form::open(['route' => 'incident.store', 'method' => 'post', 'class' => 'form form-vertical']) !!}
    @include('incidents.common.form')
    {!! Form::close() !!}
</x-app-layout>
