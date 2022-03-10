(() => {
    const capitalizeStatus = (string) => {
        if (string === "to_location")
            string = "in transit";
        return string.charAt(0).toUpperCase()  + string.slice(1)
    };
    const getInfoWindow = (markerArrPos, infowindow) => {
        const markerData = markersArray[markerArrPos];
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
                    //`<p><strong>Carrier:</strong> ${markerData.carrier.name}<br>` +
                    `<strong>Driver:</strong> ${markerData.driver.name}<br>` +
                    `<strong>Truck#:</strong> ${res.truck.number}</p>` +
                    `<strong>Coords:</strong> ${markerData.coords}</p>` +
                    `<strong>Date:</strong> ${moment(markerData.poly.info.date).format('MM/DD/YYYY HH:mm')}<br>`;
                infowindow.setContent(info);
                markerData.infowindow = infowindow;
            },
            error: () => {
                throwErrorMsg();
            }
        });
    }
    const addMarker = (data) => {
        let markerObj = {
            position: data.position,
            map,
            animation: google.maps.Animation.DROP,
            zIndex: 100,
            icon: {
                url: "/images/app/tracking/icons/delivery-truck.svg",
                scaledSize: new google.maps.Size(40, 40), // scaled size
            },
        };
        const marker = new google.maps.Marker(markerObj);
        let polyMarkers = [];
        if (Object.entries(data.poly.drivenPath).length > 0) {
            const polyArray = data.poly.drivenPath.getPath().getArray();
            polyArray.forEach((item, i) => {
                const info = data.poly.info[i];
                if ((i + 1) < polyArray.length) {
                    const lat = item.lat(),
                        lng = item.lng();
                    const markerObj = {
                        position: {lat,lng},
                        map,
                        animation: google.maps.Animation.DROP,
                    };
                    // Setting a different starting pin to be able to visualize the first position
                    if (i === 0) {
                        markerObj.icon = {
                            url: "/images/app/tracking/icons/start-pin.svg",
                            scaledSize: new google.maps.Size(40, 40), // scaled size
                        };
                    }
                    const marker = new google.maps.Marker(markerObj);
                    const content = `<strong>Date:</strong> ${moment(info.date).format('MM/DD/YYYY HH:mm')}<br>` +
                        `<strong>Coords:</strong> ${lat}, ${lng}<br>` +
                        `<strong>MPH:</strong> 0`;
                    const infowindow = new google.maps.InfoWindow({
                        content,
                    });
                    marker.infowindow = infowindow;
                    marker.addListener("click", () => {
                        infowindow.open({
                            anchor: marker,
                            map,
                            shouldFocus: true,
                        });
                    });
                    polyMarkers.push(marker);
                }
            });
        }
        markersArray.push({
            driver: data.driver,
            carrier: data.carrier,
            load: {id: data.load},
            coords: `${data.position.lat}, ${data.position.lng}`,
            marker,
            poly: data.poly,
            polyMarkers,
        });
        const arrPos = markersArray.length - 1;
        marker.addListener("click", () => {
            const markerData = markersArray[arrPos];
            if (!markerData.infowindow) {
                const infowindow = new google.maps.InfoWindow({
                    content: `<div class="p-2"><div class="spinner-border text-secondary" role="status"></div></div>`,
                });
                infowindow.open({
                    anchor: marker,
                    map,
                    shouldFocus: true,
                });
                getInfoWindow(arrPos, infowindow);
            } else {
                markerData.infowindow.open({
                    anchor: markerData.marker,
                    map,
                    shouldFocus: true,
                });
            }
        });
        return marker;
    };
    const initMapSetting = () => {
        return new google.maps.Map(document.getElementById("map"), {
            center: { lat: 32.7096373, lng: -103.1525477 },
            zoom: 10,
            disableDefaultUI: true,
            zoomControl: true,
            fullscreenControl: true,
        });
    }
    let map = initMapSetting();
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
    const dateRange = $('#dateRange');
    dateRange.daterangepicker({
        singleDatePicker: true,
        format: 'YYYY/MM/DD',
    }, (start, end, label) => {
        getData(start, end);
    });
    const driverSel = $('[name=driver]');
    const loadSel = $('[name=load]');
    const hideMarkerAndPath = (item) => {
        item.marker.setMap(null);
        if (item.poly.drivenPath && Object.keys(item.poly.drivenPath).length > 0) {
            item.poly.drivenPath.setMap(null);
        }
        if (item.polyMarkers.length > 0) {
            item.polyMarkers.forEach(marker => {
                marker.setMap(null);
            });
        }
    }
    const showMarkerAndPath = (item) => {
        item.marker.setMap(map);
        if (item.poly.drivenPath && Object.keys(item.poly.drivenPath).length > 0) {
            item.poly.drivenPath.setMap(map);
        }
        if (item.polyMarkers.length > 0) {
            item.polyMarkers.forEach(marker => {
                marker.setMap(map);
            });
        }
    }
    driverSel.select2({
        placeholder: 'Select',
        allowClear: true,
    }).on('select2:select', (e) => {
        const driver_id = Number(e.params.data.id);
        markersArray.forEach(item => {
            if (item.driver.id !== driver_id) {
                hideMarkerAndPath(item);
            } else {
                showMarkerAndPath(item);
            }
        });
    }).on('select2:unselect', () => {
        markersArray.forEach(item => {
            showMarkerAndPath(item);
        });
    });
    loadSel.select2({
        placeholder: 'Select',
        allowClear: true,
    }).on('select2:select', (e) => {
        const load_id = Number(e.params.data.id);
        markersArray.forEach(item => {
            if (item.load.id !== load_id) {
                hideMarkerAndPath(item);
            } else {
                showMarkerAndPath(item);
            }
        });
    }).on('select2:unselect', () => {
        markersArray.forEach(item => {
            showMarkerAndPath(item);
        });
    });
    let markersArray = [];
    const generatePastelColor = () => {
        let R = Math.floor((Math.random() * 127) + 127);
        let G = Math.floor((Math.random() * 127) + 127);
        let B = Math.floor((Math.random() * 127) + 127);

        let rgb = (R << 16) + (G << 8) + B;
        return `#${rgb.toString(16)}`;
    }
    const setMapData = (data) => {
        driverSel.html('<option></option>').trigger('change');
        loadSel.html('<option></option>').trigger('change');
        let drivers = [];
        let loads = [];
        data.forEach((item) => {
            drivers.push({
                id: item.id,
                text: item.name,
            });
            const locations = item.locations;
            const carrier = item.carrier;
            const loadPath = [];
            locations.forEach((location, i) => {
                if (!loads.find(load => load.id === location.parent_load.id))
                    loads.push(location.parent_load);
                const position = {lat: Number(location.latitude), lng: Number(location.longitude)};
                const foundPath = loadPath.find(obj => {
                    return obj.id === location.load_id;
                });
                if (foundPath) {
                    foundPath.data.push(position);
                    foundPath.markersInfo.push({
                        date: location.created_at,
                    })
                } else {
                    loadPath.push({
                        id: location.load_id,
                        data: [position],
                        markersInfo: [{
                            date: location.created_at,
                        }],
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
                let drivenPath = {};
                if (pathData.length > 1) {
                    drivenPath = new google.maps.Polyline({
                        path: pathData,
                        geodesic: true,
                        strokeColor: generatePastelColor(),
                        strokeOpacity: 1.0,
                        strokeWeight: 2,
                        icons: [{
                            icon: {
                                path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                            },
                            repeat: '35px',
                            offset: '100%'
                        }],
                    });
                    drivenPath.setMap(map);
                }
                const markerData = {
                    position: pathData[pathData.length - 1],
                    driver: path.driver,
                    carrier: path.carrier,
                    load: path.id,
                    poly: {
                        drivenPath,
                        info: path.markersInfo,
                    },
                };
                addMarker(markerData);
            });
        });
        drivers.forEach(item => {
            driverSel.append(`<option value="${item.id}">${item.text}</option>`);
        });
        driverSel.trigger('change');
        loads.forEach(item => {
            loadSel.append(`<option value="${item.id}">Control #${item.control_number}</option>`);
        });
        loadSel.trigger('change');
        if (!bounds.isEmpty())
            map.fitBounds(bounds);
        else
            map.setZoom(6);
    }
    const getData = (start = dateRange.data().daterangepicker.startDate, end = dateRange.data().daterangepicker.endDate) => {
        if (markersArray.length > 0) {
            map = initMapSetting();
            markersArray = [];
        }
        $.ajax({
            url: '/tracking/historyData',
            type: 'GET',
            data: {
                start: start.format('YYYY/MM/DD'),
                end: end.format('YYYY/MM/DD'),
                driver: $("[name='driver']").val(),
            },
            success: (res) => {
                setMapData(res);
            },
            error: () => {
                throwErrorMsg();
            }
        });
    };
    getData();
})();
