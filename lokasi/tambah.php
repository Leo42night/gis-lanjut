<?php
include 'poi.php';

$url = "http://" . $_SERVER['HTTP_HOST'] . "/gis-lanjut/";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form Pendaftaran Peserta</title>
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
    <nav class="d-flex flex-column my-2">
      <div class="d-flex gap-2">
        <a href="<?= $url; ?>">
          <h4>Data POI</h4>
        </a>
        <a href="<?= $url; ?>crud.php">
          <h4>Kembali</h4>
        </a>
      </div>
      <div class="mt-2 d-flex justify-content-between">
        <form id="poi-form" class="d-flex gap-2 flex-wrap">
          <input type="text" id="name" name="name" placeholder="POI Name" required>
          <input type="text" id="description" name="description" placeholder="Description" required>
          <input type="double" id="lat" name="lat" placeholder="Latitude">
          <input type="double" id="lng" name="lng" placeholder="Longitude">
          <button type="submit" class="btn btn-primary">Tambah Data</button>
        </form>
      </div>
    </nav>

    <div id="map"></div>

    <script>
      let center = { lat: -6.200000, lng: 106.816666 }; // Jakarta sebagai contohconst center = { lat: -6.200000, lng: 106.816666 }; // Jakarta sebagai contoh
      let map, markers = [];

      function initMap() {
        //draw map
        map = new google.maps.Map(document.getElementById('map'), {
          center: center,
          zoom: 13,
          mapId: 'storied-deck-432408-h3'
        });

        // Create a custom marker element
        const markerElement = document.createElement('h1');
        markerElement.innerHTML = '📍';  // Example marker content
        markerElement.style.cursor = 'pointer'; // Change cursor to pointer

        // marker center 
        marker = new google.maps.marker.AdvancedMarkerElement({
          map,
          title: "Posisi yang ingin ditambah!",
          content: markerElement,
        });

        // Fetch pertama data dari PHP dan tampilkan PoI
        fetchDataAndDisplayPOI();

        map.addListener('click', (event) => {
          marker.position = event.latLng; // Mengubah posisi marker
          const lat = marker.position.lat;
          const lng = marker.position.lng;
          document.getElementById('lat').value = lat;
          document.getElementById('lng').value = lng;
          console.log(lat, lng);
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
          .then(datas => {
            datas.forEach(poi => {
              const marker = new google.maps.marker.AdvancedMarkerElement({
                position: { lat: poi.lat, lng: poi.lng },
                map: map,
                title: poi.name,
              });
              markers.push(marker);
            });
          });
      }

      //select data of collunm (update first step)
      if (typeof recordData !== 'undefined') {
        document.getElementById('name').value = recordData.name;
        document.getElementById('description').value = recordData.description;
        document.getElementById('lat').value = recordData.lat;
        document.getElementById('lng').value = recordData.lng;
        // Populate other form fields similarly
      }


      // menambahkan data ke db
      document.getElementById('poi-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const name = document.getElementById('name').value;
        const description = document.getElementById('description').value;
        const lat = document.getElementById('lat').value;
        const lng = document.getElementById('lng').value;
        console.log(`name=${name}&description=${description}&lat=${lat}&lng=${lng}`);
        if (lat != 0 || lng != 0) {
          fetch('poi.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `name=${name}&description=${description}&lat=${lat}&lng=${lng}`
          })
            .then(response => response.text())
            .then(data => {
              alert(data);
              location.reload();
            })
            .catch(error => console.error('Error:', error));
        } else {
          alert(`Pilih Poisisi di Map \nLat: ${lat}, Lng: ${lng}`);
        }
      });

    </script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAgGBjlEnlrlO2KdsQMFL70E_Ppo3GmFPs&loading=async&callback=initMap&libraries=marker"
      async type="text/javascript" defer></script>
  </div>

</body>

</html>