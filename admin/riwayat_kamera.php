<?php 
    include './../crud/kamera.php';
    if (isset($_POST['deleteKamera'])) {
        $id = $_POST['deleteKamera'];
        $data = mysqli_query($koneksi, "SELECT * FROM log_kamera_delete WHERE lkd_id = '$id'");
        $data = mysqli_fetch_array($data);

        $merk = $data['merk'];
        $harga = $data['harga'];
        $stok = $data['stok'];
        $gambar = $data['gambar'];
        $resolusi = $data['resolusi'];
        $sensor = $data['sensor'];
        $iso_max = $data['iso_max'];

        $queryKamera = mysqli_query($koneksi, "INSERT INTO kamera VALUES ('','$merk','$harga','$stok','$gambar')");
        if ($queryKamera) {
            $idtadi = mysqli_insert_id($koneksi);
            $querySpesifikasi = mysqli_query($koneksi, "INSERT INTO spesifikasi VALUES ('','$idtadi','$resolusi','$sensor','$iso_max')");
            if ($querySpesifikasi) {
                $hapus = mysqli_query($koneksi, "DELETE FROM log_kamera_delete WHERE lkd_id = '$id'");
            }
        }
    }
?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-3 mt-1 flex-wrap">
        <h1 class="h3 text-gray-900 mb-0">Data Riwayat Kamera</h1>
        <form class="form-inline my-2 my-md-0">
            <div class="input-group">
                <input type="text" class="form-control bg-light border-2 border-primary small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" style="width: 300px;">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <!-- Data Tales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Riwayat Kamera</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-gray-900" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Merk</th>
                            <th>Harga perhari</th>
                            <th>Stok</th>
                            <th>Spesifikasi</th>
                            <th class="text-center">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $query = mysqli_query($koneksi, "SELECT * FROM log_kamera_delete");
                            if(mysqli_num_rows($query) > 0) { 
                                foreach ($query as $key => $k) { 
                        ?>
                        <tr>
                            <td><?= $key+1; ?></td>
                            <td><?= $k['merk']; ?></td>
                            <td>Rp. <?= number_format($k['harga'], 0, ',', '.'); ?></td>
                            <td><?= $k['stok']; ?></td>
                            <td>
                                Resolusi : <?= $k['resolusi']; ?> <br> 
                                Sensor : <?= $k['sensor']; ?> <br>
                                Iso Max : <?= $k['iso_max']; ?>
                            </td>
                            <td>
                                <button data-toggle="modal" data-target="#modalRestore<?= $k['lkd_id']; ?>" class="btn btn-warning" style="width: 37px; padding-left: 10px;"><i class="fas fa-arrow-alt-circle-left"></i></button>
                            </td>
                        </tr>
                        <!-- Modal Restore -->
                        <div class="modal fade text-gray-900" id="modalRestore<?= $k['lkd_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Restore Data</h5>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form action="" method="post">
                                        <div class="modal-body">
                                            <p>Restore</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-warning" value="<?= $k['lkd_id']; ?>" type="submit" name="deleteKamera">Restore</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php }} else { ?>
                        <tr>
                            <td colspan="6"><center>Tidak ada data</center></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>