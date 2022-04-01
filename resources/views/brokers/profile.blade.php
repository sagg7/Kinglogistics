<x-app-layout>
    <x-slot name="crumb_section">Profile</x-slot>

    @section('head')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endsection

    @section('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script src="{{ asset('js/common/filesUploads.min.js?1.0.1') }}"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}&libraries=places"></script>
        <script>
            const equipment = @json($equipment->message_json ?? null);
            const services = @json($service->message_json ?? null);
            const canvases = [
                {canvas: document.getElementById('signature')},
            ];
            const signature = "{{ $company->signature }}";
            (() => {
                const coords = $('[name=coords]');
                const mapProperties = {
                    center: { lat: 39.8097343, lng: -98.5556199 },
                    zoom: 10,
                    disableDefaultUI: true,
                    zoomControl: true,
                    fullscreenControl: true,
                };
                const map = new google.maps.Map(document.getElementById('mapLocation'), mapProperties);
                const handleLocationError = (browserHasGeolocation) => {
                    browserHasGeolocation
                        ? throwErrorMsg("Error: The Geolocation service failed.")
                        : throwErrorMsg("Error: Your browser doesn't support geolocation.")
                };
                const marker = new google.maps.Marker({
                    map,
                    draggable: true,
                    animation: google.maps.Animation.DROP,
                });
                const setPreset = (val, marker) => {
                    const coords = val.split(","),
                        position = {lat:Number(coords[0]),lng:Number(coords[1])};
                    marker.setPosition(position);
                    map.setCenter(position)
                }
                if (coords.val() !== "")
                    setPreset(coords.val(), marker);
                const locationButton = document.createElement("button");
                locationButton.type = 'button';
                const locate = (marker, map) => {
                    // Try HTML5 geolocation.
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                const pos = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude,
                                };
                                marker.setPosition(pos);
                                map.setCenter(pos);
                            },
                            () => {
                                handleLocationError(true);
                            }
                        );
                    } else {
                        // Browser doesn't support Geolocation
                        handleLocationError(false);
                    }
                }
                locationButton.className = "custom-map-control-button";
                locationButton.innerHTML = '<i class="feather icon-crosshair"></i>';
                map.controls[google.maps.ControlPosition.TOP_RIGHT].push(locationButton);
                locationButton.addEventListener("click", () => {
                    locate(marker, map);
                });
                map.addListener('click', (e) => {
                    marker.setPosition(e.latLng);
                });
                $('#profileForm').submit((e) => {
                    const position = marker.getPosition();
                    coords.val(`${position.lat()},${position.lng()}`);
                });
            })();

            (() => {
                const toolbarOptions = [
                    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video'],

                    [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                    //[{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    //[{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                    //[{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                    [{ 'direction': 'rtl' }],                         // text direction

                    //[{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                    //[{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                    //[{ 'font': [] }],
                    [{ 'align': [] }],

                    ['link', 'image'],

                    ['clean'],                                       // remove formatting button
                ];
                const eq_quill = new Quill('#equipment_message_quill', {
                    modules: { toolbar: toolbarOptions },
                    theme: 'snow'
                });
                if (typeof equipment != "undefined")
                    eq_quill.setContents(equipment);

                const serv_quill = new Quill('#service_message_quill', {
                    modules: { toolbar: toolbarOptions },
                    theme: 'snow'
                });
                if (typeof services != "undefined")
                    serv_quill.setContents(services);

                const submitQuill = (e, type) => {
                    e.preventDefault();
                    let quill;
                    switch (type) {
                        case 'equipment':
                        default:
                            quill = eq_quill;
                            break;
                        case 'service':
                            quill = serv_quill;
                            break;
                    }
                    const form = $(e.currentTarget),
                        message = JSON.stringify(quill.getContents());
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: {
                            title: form.find('[name=title]').val(),
                            message,
                        },
                        success: (res) => {
                            if (!res.success)
                                throwErrorMsg();
                        },
                        error: (res) => {
                            let errors = `<ul class="text-left">`;
                            Object.values(res.responseJSON.errors).forEach((error) => {
                                errors += `<li>${error}</li>`;
                            });
                            errors += `</ul>`;
                            throwErrorMsg(errors, {timer: false});
                        },
                    }).always(() => {
                        removeAjaxLoaders();
                    });
                }
                $('#equipmentForm').submit((e) => {
                    submitQuill(e, 'equipment');
                });
                $('#serviceForm').submit((e) => {
                    submitQuill(e, 'service');
                });
            })();

            (() => {
                $('#rentalsForm').submit(e => {
                    e.preventDefault();
                    const form = $(e.currentTarget);
                    const formData = new FormData(form[0]);
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: (res) => {
                            if (!res.success)
                                throwErrorMsg();
                        },
                        error: () => {
                            throwErrorMsg();
                        }
                    }).always(() => {
                        removeAjaxLoaders();
                    });
                });
            })();
        </script>
        <script src="{{ asset('js/common/initSignature.min.js?1.0.2') }}"></script>
    @endsection
    @section('modals')
        <div class="modal fade" id="viewSignature" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content" style="max-height: calc(100vh - 3.5rem);">
                    <div class="modal-header">
                        <h5 class="modal-title">Signature</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ $company->signature }}" alt="signature">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-block mr-1 mb-1 waves-effect waves-light" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @component('components.nav-pills-form', ['pills' => [
    ['name' => 'Information', 'pane' => 'pane-info'],
    ['name' => 'Equipment', 'pane' => 'pane-equipment'],
    ['name' => 'Services', 'pane' => 'pane-service'],
    ['name' => 'Rentals', 'pane' => 'pane-rentals'],
    ]])
        <div role="tabpanel" class="tab-pane active" id="pane-info" aria-expanded="true">
            {!! Form::open(['route' => ['company.update', $company->id ?? 1], 'method' => 'post', 'class' => 'form form-vertical with-sig-pad', 'enctype' => 'multipart/form-data', 'id' => 'profileForm']) !!}
            @include('brokers.common.form')
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-equipment" aria-expanded="false">
            {!! Form::open(['route' => ['company.equipment', $company->id ?? 1], 'method' => 'post', 'class' => 'form form-vertical', 'enctype' => 'multipart/form-data', 'id' => 'equipmentForm']) !!}
            @include('brokers.common.quillForm', ['title' => $equipment->title ?? null, 'id' => 'equipment'])
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-service" aria-expanded="false">
            {!! Form::open(['route' => ['company.service', $company->id ?? 1], 'method' => 'post', 'class' => 'form form-vertical', 'enctype' => 'multipart/form-data', 'id' => 'serviceForm']) !!}
            @include('brokers.common.quillForm', ['title' => $service->title ?? null, 'id' => 'service'])
            {!! Form::close() !!}
        </div>
        <div role="tabpanel" class="tab-pane" id="pane-rentals" aria-expanded="false">
            {!! Form::open(['route' => ['company.rentals', $company->id ?? 1], 'method' => 'post', 'class' => 'form form-vertical', 'id' => 'rentalsForm']) !!}
            @include('brokers.common.rentals', ['title' => $service->title ?? null, 'id' => 'rentals'])
            {!! Form::close() !!}
        </div>
    @endcomponent
</x-app-layout>
