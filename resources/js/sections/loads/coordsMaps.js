(() => {
    const interactive = typeof readOnly === "undefined";
    const coordsInpO = $('[name=origin_coords]');
    const coordsInpD = $('[name=destination_coords]');
    const mapProperties = {
        center: { lat: 39.8097343, lng: -98.5556199 },
        zoom: 5,
        disableDefaultUI: true,
        zoomControl: true,
        fullscreenControl: true,
    };
    const mapO = new google.maps.Map(document.getElementById('mapOrigin'), mapProperties);
    const mapD = new google.maps.Map(document.getElementById('mapDestination'), mapProperties);
    let handleLocationError = (browserHasGeolocation) => {
        browserHasGeolocation
            ? throwErrorMsg("Error: Hay un error con el servicio de geolocalización.")
            : throwErrorMsg("Error: Tu navegador no soporta el servicio de geolocalización.")
    }
    const markerO = new google.maps.Marker({
        map: mapO,
        draggable: interactive,
        animation: google.maps.Animation.DROP,
    });
    const markerD = new google.maps.Marker({
        map: mapD,
        draggable: interactive,
        animation: google.maps.Animation.DROP,
    });
    const setPreset = (val, marker) => {
        const coords = val.split(",");
        marker.setPosition({lat:Number(coords[0]),lng:Number(coords[1])});
    }
    coordsInpO.change(() => {
        setPreset(coordsInpO.val(), markerO);
    });
    coordsInpD.change(() => {
        setPreset(coordsInpD.val(), markerD);
    });
    if (coordsInpO.val() !== "")
        setPreset(coordsInpO.val(), markerO);
    if (coordsInpD.val() !== "")
        setPreset(coordsInpD.val(), markerD);
    if (interactive) {
        const locationButton = document.createElement("button");
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
        mapO.controls[google.maps.ControlPosition.TOP_RIGHT].push(locationButton);
        locationButton.addEventListener("click", () => {
            locate(markerO, mapO);
        });
        const locationButtonD = locationButton.cloneNode(true);
        mapD.controls[google.maps.ControlPosition.TOP_RIGHT].push(locationButtonD);
        locationButtonD.addEventListener("click", () => {
            locate(markerD, mapD);
        });
        mapO.addListener('click', (e) => {
            markerO.setPosition(e.latLng);
        });
        mapD.addListener('click', (e) => {
            markerD.setPosition(e.latLng);
        });
        $('#loadForm').submit((e) => {
            const positionO = markerO.getPosition();
            const positionD = markerD.getPosition();
            coordsInpO.val(`${positionO.lat()},${positionO.lng()}`);
            coordsInpD.val(`${positionD.lat()},${positionD.lng()}`);
        });
    }
})();
