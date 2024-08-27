<?php
$url = "http://" . $_SERVER['HTTP_HOST'] . "/gis-lanjut/";
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
            <a href="<?= $url ?>crud.php" class="btn btn-secondary" role="button">CRUD Lokasi</a>
        </nav>

        <!-- Kontrol untuk mengubah radius -->
        <div id="controls">
            <label for="radiusInput">Radius (meter): </label>
            <input class="w-100" type="range" id="radiusInput" min="500" max="5000" step="1 0" value="5000">
            <span id="radiusValue">1000</span> meter
        </div>
        <div id="map"></div>

        <script>
            let center = { lat: -6.200000, lng: 106.816666 }; // Jakarta sebagai contohconst center = { lat: -6.200000, lng: 106.816666 }; // Jakarta sebagai contoh
            let map, circle, markers = [];
            const radiusInput = document.getElementById('radiusInput');
            const radiusValue = document.getElementById('radiusValue');
            let marker;

            function initMap() {
                //draw map
                map = new google.maps.Map(document.getElementById('map'), {
                    center: center,
                    zoom: 13
                });

                // marker center
                marker = new google.maps.Marker({
                    position: center,
                    map,
                    title: "tengah Lokasi!",
                    draggable: true,
                    icon: {
                        path: google.maps.SymbolPath.CIRCLE,
                        scale: 10,
                    },
                });

                circle = new google.maps.Circle({
                    map: map,
                    radius: parseInt(radiusInput.value), // Radius dari input range
                    center: center,
                    fillColor: '#AA0000',
                    fillOpacity: 0.2,
                    strokeColor: '#AA0000',
                    strokeOpacity: 0.5,
                    strokeWeight: 2
                });

                // Fetch pertama data dari PHP dan tampilkan PoI
                fetchDataAndDisplayPOI();

                // Event listener untuk mengubah radius ketika input berubah
                radiusInput.addEventListener('input', function () {
                    const newRadius = parseInt(radiusInput.value);
                    radiusValue.textContent = newRadius;
                    circle.setRadius(newRadius);
                    fetchDataAndDisplayPOI(); // Refresh PoI sesuai radius baru
                });

                // pilih posisi intu input
                google.maps.event.addListener(marker, 'position_changed', function () {
                    const lat = marker.getPosition().lat();
                    const lng = marker.getPosition().lng();
                    circle.setCenter({ lat, lng });
                    fetchDataAndDisplayPOI(); //data marker in radius berubah secara realtime
                    console.log(circle.getCenter().lat(), circle.getCenter().lng());
                });

                map.addListener('click', (event) => {
                    marker.setPosition(event.latLng);
                });
            }

            function drawCircle(lat, lng) {
                let center = new google.maps.LatLng(lat, lng);

            }

            function fetchDataAndDisplayPOI() {
                // Bersihkan marker sebelumnya
                markers.forEach(marker => marker.setMap(null));
                markers = [];

                fetch('ambil.php')
                    .then(response => response.json())
                    .then(data => {
                        const filteredData = data.filter(poi => {
                            const distance = calculateDistance({ lat: circle.getCenter().lat(), lng: circle.getCenter().lng() }, { lat: poi.lat, lng: poi.lng });
                            return distance <= circle.getRadius(); // Filter berdasarkan radius lingkaran saat ini
                        });

                        filteredData.forEach(poi => {
                            const marker = new google.maps.Marker({
                                position: { lat: poi.lat, lng: poi.lng },
                                map: map,
                                title: poi.name,
                            });
                            markers.push(marker);
                        });
                    });
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