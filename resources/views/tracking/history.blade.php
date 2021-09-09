<x-app-layout>
    <x-slot name="crumb_section">Tracking</x-slot>
    <x-slot name="crumb_subsection">History</x-slot>

    @section('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ env("GOOGLE_MAPS_API") }}"></script>
        <script>
            const data = @json($data);
            const company = @json($company);
            (() => {
                const capitalizeStatus = (string) => {
                    if (string === "to_location")
                        string = "in transit";
                    return string.charAt(0).toUpperCase()  + string.slice(1)
                };
                const getInfoWindow = (markerArrPos) => {
                    const markerData = markersArray[markerArrPos];
                    if (!markerData.infowindow) {
                        $.ajax({
                            url: '/tracking/getPinLoadData',
                            type: 'GET',
                            data: {
                                load: markerData.load.id,
                            },
                            success: (res) => {
                                const info = (res.shipper.name ? `<p><strong>Shipper:</strong> ${res.shipper.name}</p>` : '') +
                                    `<p><strong>Status:</strong> ${capitalizeStatus(res.status)}<br>` +
                                    (res.origin ? `<strong>Origin:</strong> ${res.origin}<br><strong>Destination:</strong> ${res.destination}</p>` : '') +
                                    `<p><strong>Carrier:</strong> ${markerData.carrier.name}<br>` +
                                    `<strong>Driver:</strong> ${markerData.driver.name}<br>` +
                                    `<strong>Truck#:</strong> ${res.truck.number}`+
                                    `<strong>MPH:</strong> 52MPH</p>` +
                                    `<strong>Coords:</strong> ${markerData.coords}</p>`;
                                const infowindow = new google.maps.InfoWindow({
                                    content: info,
                                });
                                markerData.infowindow = infowindow;
                                infowindow.open({
                                    anchor: markerData.marker,
                                    map,
                                    shouldFocus: true,
                                });
                            },
                            error: () => {
                                throwErrorMsg();
                            }
                        });
                    } else {
                        markerData.infowindow.open({
                            anchor: markerData.marker,
                            map,
                            shouldFocus: true,
                        });
                    }
                }
                const addMarker = (data) => {
                    let markerObj = {
                        position: data.position,
                        map,
                        animation: google.maps.Animation.DROP,
                        icon: {
                            url: "/images/app/tracking/icons/delivery-truck.svg",
                            scaledSize: new google.maps.Size(35, 35), // scaled size
                        },
                    };
                    const marker = new google.maps.Marker(markerObj);
                    markersArray.push({
                        driver: data.driver,
                        carrier: data.carrier,
                        load: {id: data.load},
                        coords: `${data.position.lat}, ${data.position.lng}`,
                        marker,
                    });
                    const arrPos = markersArray.length - 1;
                    marker.addListener("click", () => {
                        getInfoWindow(arrPos);
                    });

                    return marker;
                };
                const map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: 39.8097343, lng: -98.5556199 },
                    zoom: 10,
                    disableDefaultUI: true,
                    zoomControl: true,
                    fullscreenControl: true,
                });
                const bounds = new google.maps.LatLngBounds();
                if (company) {
                    const info = (company.name ? `<p><strong>Company:</strong> ${company.name}</p>` : '') +
                        (company.contact_phone ? `<p></p><strong>Phone:</strong> ${company.contact_phone}</p>` : '') +
                        (company.email ? `<p></p><strong>Email:</strong> ${company.email}</p>` : '') +
                        (company.address ? `<p></p><strong>Address:</strong> ${company.address}</p>` : '');
                    const infowindow = new google.maps.InfoWindow({
                        content: info,
                    });
                    const coords = company.location.split(","),
                        position = {lat:Number(coords[0]),lng:Number(coords[1])};
                    const markerObj = {
                        position: position,
                        map,
                        animation: google.maps.Animation.DROP,
                        icon: {
                            url: "/images/app/logos/logo-dark-simple.png",
                            scaledSize: new google.maps.Size(30, 30), // scaled size
                        },
                    };
                    const marker = new google.maps.Marker(markerObj);
                    marker.addListener("click", () => {
                        infowindow.open({
                            anchor: marker,
                            map,
                            shouldFocus: true,
                        });
                    });
                    bounds.extend(marker.position);
                }
                const markersArray = [];
                const polyLines = [];
                data.forEach((item) => {
                    const locations = item.locations;
                    const carrier = item.carrier;
                    const loadPath = [];
                    locations.forEach((location, i) => {
                        const position = {lat: Number(location.latitude), lng: Number(location.longitude)};
                        const foundPath = loadPath.find(obj => {
                            return obj.id === location.load_id;
                        });
                        if (foundPath) {
                            foundPath.data.push(position);
                        } else {
                            loadPath.push({
                                id: location.load_id,
                                data: [position],
                                driver: {
                                    id: item.id,
                                    name: item.name,
                                },
                                carrier: {
                                    id: carrier.id,
                                    name: carrier.name,
                                },
                            });
                        }
                        bounds.extend(position);
                    });
                    loadPath.forEach(path => {
                        const pathData = path.data;
                        if (pathData.length > 1) {
                            const drivenPath = new google.maps.Polyline({
                                path: pathData,
                                geodesic: true,
                                strokeColor: "#FF0000",
                                strokeOpacity: 1.0,
                                strokeWeight: 2,
                            });
                            drivenPath.setMap(map);
                        }
                        const markerData = {
                            position: pathData[pathData.length - 1],
                            driver: path.driver,
                            carrier: path.carrier,
                            load: path.id,
                        };
                        addMarker(markerData);
                    });
                });
                if (!bounds.isEmpty())
                    map.fitBounds(bounds);
                else
                    map.setZoom(6);
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
