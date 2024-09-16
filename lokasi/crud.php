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
        <nav class="d-flex justify-content-between mt-2">
            <div class="d-flex gap-2">
                <a href="<?= $url; ?>">
                    <h4>Data POI</h4>
                </a>
                <a href="<?= $url; ?>crud.php">
                    <h4 class="fw-lighter">CRUD</h4>
                </a>
            </div>
            <a href="<?= $url; ?>tambah.php" class="btn btn-primary">Tambah Data</a>
        </nav>
        <table class="my-3 table table-bordered">
            <tr class="table-danger">
                <thead class="table-primary">
                    <th>id</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th colspan='2'>Aksi</th>
                </thead>
                <?php
                foreach (getData() as $data) {
                    ?>
                    <tbody>
                        <tr>
                            <td><?= $data["id"]; ?></td>
                            <td><?= $data["name"]; ?></td>
                            <td><?= $data["description"]; ?></td>
                            <td><?= $data["lat"]; ?></td>
                            <td><?= $data["lng"]; ?></td>
                            <td>
                                <a href="<?= $url ?>ubah.php?id=<?= urlencode($data['id']); ?>" class="btn btn-warning me-2"
                                    onclick="updateLoc(<?= $data['id']; ?>)">Update</href=>
                                <a href="<?= $url ?>crud.php?delete=<?= urlencode($data['id']); ?>"
                                    class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    </tbody>

                    <?php
                }
                // Debugging the variables
                ?>
        </table>
    </div>
</body>

</html>