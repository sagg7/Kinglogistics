<x-app-layout>
    <x-slot name="crumb_section">Load</x-slot>
    <x-slot name="crumb_subsection">Location</x-slot>

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}"></script>
        <script>
            const locations = @json($load->locations);
            (() => {
                const map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: 39.8097343, lng: -98.5556199 },
                    zoom: 10,
                    disableDefaultUI: true,
                    zoomControl: true,
                    fullscreenControl: true,
                });
                let polyPath = [];
                locations.forEach((location, i) => {
                    const markerPosition = {lat: Number(location.latitude), lng: Number(location.longitude)};
                    let markerObj = {
                        position: markerPosition,
                        map,
                        animation: google.maps.Animation.DROP,
                    };
                    if (i === (locations.length - 1))
                        _.merge(markerObj, {
                            label: {
                                fontFamily: "'Font Awesome 5 Free'",
                                fontWeight: '900',
                                fontSize: "3em",
                                text: "\uf0d1",
                                color: '#10163A',
                            },
                            icon: '  ',
                        });
                    new google.maps.Marker(markerObj);
                    polyPath.push(markerPosition);
                });
                const travelPath = new google.maps.Polyline({
                    path: polyPath,
                    geodesic: true,
                    strokeColor: "#10163A",
                    strokeOpacity: 1.0,
                    strokeWeight: 2,
                    map,
                });
                map.setCenter(polyPath[polyPath.length - 1]);
            })();
        </script>
    @endsection

    <div class="card">
        <div class="card-body">
            <div class="card-content">
                <div id="map" style="width: 100%; height: calc(100vh - 265px);"></div>
            </div>
        </div>
    </div>
</x-app-layout>
