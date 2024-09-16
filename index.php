<?php
$url = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $_SERVER['HTTP_HOST'] . "/gis-lanjut/";
// $url = "http://" . $_SERVER['HTTP_HOST'] . "/gis-lanjut/";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Interest Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.1/css/bootstrap.min.css">

    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <nav class="d-flex gap-2 mt-2">
            <!-- <a href="<?= $url ?>crud.php" class="btn btn-secondary" role="button">CRUD Lokasi</a> -->
        </nav>
        <div class="card">
            <div class="card-body px-0 pt-0 pb-2">
                <div id="reader" width="600px"></div>
                <!-- <input type="file" id="qr-input-file" accept="image/*"> -->
            </div>
        </div>

        <div class="card">
            <form action="">
                <div class="card-body px-0 pt-0 pb-2">
                    <div id="reader" width="600px"></div>
                    <!-- <input type="file" id="qr-input-file" accept="image/*"> -->
                    <div class="form-group d-flex align-items-center p-2">
                        <label for="productID" class="fs-3 text-nowrap me-2">Product ID: </label><input type="number"
                            class="form-control" id="productID" value="1234567890123">
                    </div>
                    <div class="form-group">
                        <label for="radiusInput">Radius (meter): </label>
                        <input class="w-100" type="range" id="radiusInput" min="500" max="5000" step="10" value="5000">
                        <span id="radiusValue">1000</span> meter
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
        <div id="map"></div>
    </div>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        let productID = document.getElementById('productID');

        // const html5QrCode = new Html5Qrcode("reader");
        // const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        //     /* handle success */
        // };
        // const config = { fps: 10, qrbox: { width: 250, height: 250 } };

        // // If you want to prefer front camera
        // html5QrCode.start({ facingMode: "user" }, config, qrCodeSuccessCallback);

        // // If you want to prefer back camera
        // html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);

        // // Select front camera or fail with `OverconstrainedError`.
        // html5QrCode.start({ facingMode: { exact: "user" } }, config, qrCodeSuccessCallback);

        // // Select back camera or fail with `OverconstrainedError`.
        // html5QrCode.start({ facingMode: { exact: "environment" } }, config, qrCodeSuccessCallback);

        // let html5QrcodeScanner = new Html5QrcodeScanner(
        //     "reader",
        //     { fps: 10, qrbox: { width: 250, height: 250 } },
        //     /* verbose= */ false);

        // html5QrcodeScanner.render((decodedText, decodedResult) => {
        //     productID.value = decodedText;
        // }, (error) => {
        //     console.warn(`Code scan error = ${error}`);
        // });

        // -0.05514926109037303, 109.34932199707387 tugu khatulistiwa
        let center = { lat: -0.05514926109037303, lng: 109.34932199707387 }; // Jakarta sebagai contohconst center = { lat: -6.200000, lng: 106.816666 }; // Jakarta sebagai contoh
        let map, circle, AdvanceMarker, markers = [];
        const radiusInput = document.getElementById('radiusInput');
        const radiusValue = document.getElementById('radiusValue');
        let marker;
        console.log(productID.value);

        checkProdID();
        productID.addEventListener('input', function () {
            checkProdID();
        });

        function checkProdID() {
            if (productID.value == "") {
                radiusInput.disabled = true;
            } else {
                radiusInput.disabled = false; // Enable radiusInput if productID is not empty
            }
        }

        async function initMap() {
            // Request needed libraries.
            const { Map } = await google.maps.importLibrary("maps");
            const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary(
                "marker",
            );
            AdvanceMarker = AdvancedMarkerElement; //make it global
            const { Place } = await google.maps.importLibrary("places");

            // if (navigator.geolocation) {
            //     navigator.geolocation.getCurrentPosition((position) => {
            //         center = {
            //             lat: position.coords.latitude,
            //             lng: position.coords.longitude,
            //         };
            //     })
            // } else {
            //     alert("Geolocation is not supported by this browser.\nstart using defailt value");
            // }

            map = new Map(document.getElementById("map"), {
                center,
                zoom: 13,
                mapId: 'storied-deck-432408-h3',
            });

            //membuat icon marker
            // A marker using a Font Awesome icon for the glyph.
            const icon = document.createElement("div");
            icon.innerHTML = '<i class="fa-brands fa-centercode"></i>';
            const faPin = new PinElement({
                glyph: icon,
                glyphColor: "#ff8300",
                background: "#FFD514",
                borderColor: "#ff8300",
            });
            // marker tengah lokasi
            const pinSvgMarkerView = new AdvancedMarkerElement({
                map,
                position: center,
                content: faPin.element,
                title: "center Position.",
            });

            circle = new google.maps.Circle({
                map,
                radius: parseInt(radiusInput.value), // Radius dari input range
                center,
                fillColor: '#AA0000',
                fillOpacity: 0.2,
                strokeColor: '#AA0000',
                strokeOpacity: 0.5,
                strokeWeight: 2
            });

            // Event listener untuk mengubah radius ketika input berubah
            radiusInput.addEventListener('input', function () {
                const newRadius = parseInt(radiusInput.value);
                radiusValue.textContent = newRadius; // 
                circle.setRadius(newRadius);
                fetchDataAndDisplayPOI(); // Fungsi Manajemen Lokasi Produk Termurah pada Database
            });
        }

        function fetchDataAndDisplayPOI() {
            console.log('<?= $url ?>ambil.php');
            fetch('<?= $url ?>ambil.php')
                // .then(response => response.text()) // Use .text() to inspect the raw response
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    const filteredData = data.filter(poi => {
                        //filter ID and Distance
                        const distance = calculateDistance({ lat: circle.getCenter().lat(), lng: circle.getCenter().lng() }, { lat: poi.shop_lat, lng: poi.shop_lng });
                        console.log('distance: ', distance);
                        console.log('radius: ', circle.getRadius());
                        console.log('produkID: ', productID.value);
                        console.log('poi.produkID: ', poi.productID);
                        return (distance <= circle.getRadius() && productID.value == poi.productID); // Boolean, Filter berdasarkan radius lingkaran saat ini
                    });
                    console.log('Filtered Data: ', filteredData);

                    //bersihkan marker Sebelumnya
                    clearMarker();

                    //tampilkan semua lokasi produk dalam range
                    showShops(filteredData);

                    //zoom ke harga termurah
                    temukanMurah(filteredData);
                });
        }

        // Bersihkan marker sebelumnya
        function clearMarker() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
        }

        function showShops(shops, marker) {
            let bounds = new google.maps.LatLngBounds();
            shops.forEach((shop) => {

                // Create custom HTML for AdvancedMarkerElement
                const content = document.createElement("div");
                content.className = "card rounded";
                content.innerHTML = `
                <div class="card-head text-center">$${shop.harga}</div>
                <div class="card-body p-0">${shop.shop_name}</div>
                `;

                // Create AdvancedMarkerElement
                const shopLocation = new google.maps.LatLng(shop.shop_lat, shop.shop_lng);
                const marker = new AdvanceMarker({
                    position: shopLocation,
                    map: map,
                    content: content,
                });
                console.log(marker);
                markers.push(marker);
                bounds.extend(shopLocation);

            });
            map.fitBounds(bounds);
        }

        function temukanMurah(shops) {
            console.log("before cheap", shops);
            let cheapestShop = shops.reduce((prev, curr) =>
                prev.harga < curr.harga ? prev : curr
            );
            console.log("after cheap", cheapestShop);

            // fokus ke objek
            let bounds = new google.maps.LatLngBounds();
            let shopLoc = new google.maps.LatLng(cheapestShop.shop_lat, cheapestShop.shop_lng);
            console.log(shopLoc, typeof(shopLoc));

            bounds.extend(shopLoc);
            map.fitBounds(bounds);

            // // Direction Service API masih dimatikan
            // let directionsRenderer = new google.maps.DirectionsRenderer();
            // let directionsService = new google.maps.DirectionsService();
            // directionsRenderer.setMap(map);

            // // Request directions from user to cheapest shop
            // let request = {
            //     origin: userLocation,
            //     destination: { lat: parseFloat(cheapestShop.lat), lng: parseFloat(cheapestShop.lng) },
            //     travelMode: "DRIVING",
            // };
            // console.log(request)
            // directionsService.route(request, function (result, status) {
            //     if (status == "OK") {
            //         directionsRenderer.setDirections(result);
            //     }
            // });
        }
        // Fungsi untuk menghitung jarak antara dua titik menggunakan Haversine Formula
        function calculateDistance(pointA, pointB) {
            const R = 6371e3;  // Radius bumi dalam meter
            const φ1 = pointA.lat * Math.PI / 180;
            const φ2 = pointB.lat * Math.PI / 180;
            const Δφ = (pointB.lat - pointA.lat) * Math.PI / 180;
            const Δλ = (pointB.lng - pointA.lng) * Math.PI / 180;

            const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            const distance = R * c; // Dalam meter
            return distance;
        }
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAgGBjlEnlrlO2KdsQMFL70E_Ppo3GmFPs&callback=initMap&loading=async"
        async type="text/javascript" defer></script>
</body>

</html>