(() => {
    const addMarker = (data) => {
        const capitalizeStatus = (string) => {
            if (string === "to_location")
                string = "in transit";
            return string.charAt(0).toUpperCase()  + string.slice(1)
        };
        const markerPosition = {lat: Number(data.coords.latitude), lng: Number(data.coords.longitude)};
        const info = (data.shippers.name ? `<p><strong>Shipper:</strong> ${data.shippers.name}</p>` : '') +
            `<p><strong>Status:</strong> ${capitalizeStatus(data.status)}<br>` +
            (data.load.origin ? `<strong>Origin:</strong> ${data.load.origin}<br><strong>Destination:</strong> ${data.load.destination}</p>` : '') +
            `<p><strong>Carrier:</strong> ${data.carrier.name}<br>` +
            `<strong>Driver:</strong> ${data.driver.name}<br>` +
            `<strong>Truck#:</strong> ${data.truck.number}</p>`;
        const infowindow = new google.maps.InfoWindow({
            content: info,
        });
        let markerObj = {
            position: markerPosition,
            map,
            animation: google.maps.Animation.DROP,
            icon: {
                url: "/images/app/tracking/icons/delivery-truck.svg",
                scaledSize: new google.maps.Size(50, 50), // scaled size
                origin: new google.maps.Point(0,0), // origin
                anchor: new google.maps.Point(0, 0) // anchor
            },
        };
        const marker = new google.maps.Marker(markerObj);
        marker.addListener("click", () => {
            infowindow.open({
                anchor: marker,
                map,
                shouldFocus: false,
            });
        });
        markersArray.push({
            load: {
                id: data.load.id
            },
            driver: {
                id: data.driver.id,
            },
            marker,
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
    let markersArray = [];
    data.forEach((item) => {
        const location = item.latest_location;
        const load = location.parent_load ? location.parent_load : {};
        const shipper = load.shipper ? load.shipper : {};
        const truck = load.truck ? load.truck : item.truck;
        const carrier = item.carrier;
        const data = {
            driver: {
                id: item.id,
                name: item.name,
            },
            truck: {
                number: truck.number,
            },
            carrier: {
                id: carrier.id,
                name: carrier.name,
            },
            shippers: {
                id: shipper.id,
                name: shipper.name,
            },
            coords: {
                latitude: Number(location.latitude),
                longitude: Number(location.longitude),
            },
            load: {
                origin: load.origin,
                destination: load.destination,
            },
            status: location.status,
        }
        const marker = addMarker(data);
        bounds.extend(marker.position);
    });
    map.fitBounds(bounds);

    window.Echo.private(channel)
        .listen(echoEvent, e => {
            let markerData = markersArray.find(o => o.driver.id === e.driver.id)
            if (markerData) {
                markerData.marker.setPosition({lat: Number(e.coords.latitude), lng: Number(e.coords.longitude)});
                markerData.marker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => {
                    markerData.marker.setAnimation(null);
                }, 600);
            } else {
                addMarker(e);
            }
            const bounds = new google.maps.LatLngBounds();
            markersArray.forEach(obj => {
                const marker = obj.marker;
                bounds.extend(marker.position);
            });
            map.fitBounds(bounds);
        });
})();
