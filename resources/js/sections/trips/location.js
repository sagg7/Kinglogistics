(() => {
    const interactive = typeof readOnly === "undefined";
    const coordsInp = $('[name=coords]');
    const lat = $('#lat');
    const lng = $('#lng');
    const isLatitude = num => isFinite(num) && Math.abs(num) <= 90;
    const isLongitude = num => isFinite(num) && Math.abs(num) <= 180;
    const mapProperties = {
        center: { lat: 39.8097343, lng: -98.5556199 },
        zoom: 5,
        disableDefaultUI: true,
        zoomControl: true,
        fullscreenControl: true,
    };
    const map = new google.maps.Map(document.getElementById('map'), mapProperties);
    let handleLocationError = (browserHasGeolocation) => {
        browserHasGeolocation
            ? throwErrorMsg("Error: The Geolocation service failed.")
            : throwErrorMsg("Error: Your browser doesn't support geolocation.")
    }
    const marker = new google.maps.Marker({
        map: map,
        draggable: interactive,
        animation: google.maps.Animation.DROP,
    });
    const setPreset = (val, marker) => {
        const coords = val.split(",");
        marker.setPosition({lat:Number(coords[0]),lng:Number(coords[1])});
        lat.val(coords[0]);
        lng.val(coords[1]);
    }
    coordsInp.change(() => {
        setPreset(coordsInp.val(), marker);
    });
    if (coordsInp.val() !== "") {
        setPreset(coordsInp.val(), marker);
    }
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
        locationButton.type = "button";
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(locationButton);
        locationButton.addEventListener("click", () => {
            locate(marker, map);
        });
        map.addListener('click', (e) => {
            marker.setPosition(e.latLng);
            const position = marker.getPosition();
            lat.val(position.lat());
            lng.val(position.lng());
        });
        const setMarkerPosViaInputs = () => {
            marker.setPosition({lat:Number(lat.val()),lng:Number(lng.val())});
        };
        lat.change(() => {
            if (!isLatitude(lat.val())) {
                throwErrorMsg('The latitud value is not valid');
            } else {
                if (lng.val())
                    setMarkerPosViaInputs();
            }
        });
        lng.change(() => {
            if (!isLongitude(lng.val())) {
                throwErrorMsg('The longitude value is not valid');
            } else {
                if (lat.val())
                    setMarkerPosViaInputs();
            }
        });
        $('#coordsForm').submit((e) => {
            const position = marker.getPosition();
            coordsInp.val(`${position.lat()},${position.lng()}`);
        });
    }
})();
