<x-app-layout>
    <x-slot name="crumb_section">Profile</x-slot>

    @section('scripts')
        <script src="{{ asset('js/common/filesUploads.min.js') }}"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}&libraries=places"></script>
        <script>
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
        </script>
    @endsection

    {!! Form::open(['route' => ['company.update', $company->id ?? 1], 'method' => 'post', 'class' => 'form form-vertical', 'enctype' => 'multipart/form-data', 'id' => 'profileForm']) !!}
    @include('brokers.common.form')
    {!! Form::close() !!}
</x-app-layout>
