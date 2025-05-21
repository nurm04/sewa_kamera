<?php 
    include './../crud/kamera.php';

    $errAdd = [];
    $errEdit = [];

    if (isset($_POST['addKamera'])) {
        $merk = $_POST['merk'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $resolusi = $_POST['resolusi'];
        $sensor = $_POST['sensor'];
        $iso_max = $_POST['iso_max'];

        $gambar = "";
        if(!empty($_FILES['gambar']['name'])) {
            $rand = rand();
            $filename = $_FILES['gambar']['name'];
            $gambar = $rand.'_'.$filename;
        } else {
            $gambar = "";
        }
        move_uploaded_file($_FILES['gambar']['tmp_name'], './../img/'.$gambar);

        $result = addKamera($merk, $harga, $stok, $gambar, $resolusi, $sensor, $iso_max);
    
        if ($result === true) {
            alertSucces('Tambah data berhasil.','data-kamera');
        } else if ($result === false) {
            alertDanger('Tambah data gagal.!');
        } else {
            alertDanger('Tambah data gagal, silahkan cek form tadi!');
            $errAdd = $result;
        }
    }

    if (isset($_POST['editKamera'])) {
        $id = $_POST['editKamera'];
        $merk = $_POST['merk'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $resolusi = $_POST['resolusi'];
        $sensor = $_POST['sensor'];
        $iso_max = $_POST['iso_max'];

        $gambar_old = $_POST['gambar_old'];
        $gambar = "";
        if(!empty($_FILES['gambar']['name'])) {
            $rand = rand();
            $filename = $_FILES['gambar']['name'];
            $gambar = $rand.'_'.$filename;
            move_uploaded_file($_FILES['gambar']['tmp_name'], './../img/'.$gambar);
        } else {
            $gambar = $gambar_old;
        }
    
        $result = editKamera($id, $merk, $harga, $stok, $gambar, $resolusi, $sensor, $iso_max);
    
        if ($result === true) {
            alertSucces('Ubah data berhasil.','data-kamera');
        } else if ($result === false) {
            alertDanger('Ubah data gagal.!');
        } else {
            alertDanger('Ubah data gagal, silahkan cek form tadi!');
            $errEdit = $result;
        }
    }

    if (isset($_POST['deleteKamera'])) {
        $kamera_id = $_POST['deleteKamera'];

        $result = deleteKamera($kamera_id);
    
        if ($result === true) {
            alertSucces('Hapus data berhasil.','data-kamera');
        } else {
            alertDanger($result);
        }
    }
?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-3 mt-1 flex-wrap">
        <h1 class="h3 text-gray-900 mb-0">Data Kamera</h1>
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
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Kamera</h6>
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">+ Tambah Data</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-gray-900" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Merk</th>
                            <th>Harga perhari</th>
                            <th>Stok</th>
                            <th>Spesifikasi</th>
                            <th class="text-center">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            if(mysqli_num_rows($getAllKamera) > 0) { 
                                foreach ($getAllKamera as $key => $k) { 
                        ?>
                        <tr>
                            <td><?= $key+1; ?></td>
                            <td><img src="./../img/<?= $k['gambar']; ?>" alt="<?= $k['gambar']; ?>" height="70"></td>
                            <td><?= $k['merk']; ?></td>
                            <td>Rp. <?= number_format($k['harga_sewa_perhari'], 0, ',', '.'); ?></td>
                            <td><?= $k['stok']; ?></td>
                            <td>
                                Resolusi : <?= $k['resolusi']; ?> <br> 
                                Sensor : <?= $k['sensor']; ?> <br>
                                Iso Max : <?= $k['iso_max']; ?>
                            </td>
                            <td>
                                <button data-toggle="modal" data-target="#modalUpdate<?= $k['kamera_id']; ?>" class="btn btn-warning" style="width: 37px; padding-left: 10px;"><i class="fas fa-pen"></i></button>
                                <button data-toggle="modal" data-target="#modalHapus<?= $k['kamera_id']; ?>" class="btn btn-danger" style="width: 37px; padding-left: 10px;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>

                        <!-- Modal Update -->
                        <div class="modal fade text-gray-900" id="modalUpdate<?= $k['kamera_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Update Data</h5>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <img src="./../img/<?= $k['gambar']; ?>" alt="<?= $k['gambar']; ?>" width="300">
                                            <input type="hidden" name="gambar_old" value="<?= $k['gambar']; ?>">
                                            <div class="mb-3">
                                                <label for="gambar" class="form-label">Gambar</label>
                                                <input type="file" class="form-control" name="gambar" id="gambar">
                                                <span class="text-danger"><?= isset($errEdit['gambar'])?$errEdit['gambar']:""; ?></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="merk" class="form-label">Merk</label>
                                                <input value="<?= isset($_POST['merk'])?$_POST['merk']:$k['merk']; ?>" type="text" class="form-control <?= (isset($errEdit['merk']) ? "is-invalid" : ""); ?>" name="merk" id="merk" placeholder="Merk">
                                                <span class="text-danger"><?= isset($errEdit['merk'])?$errEdit['merk']:""; ?></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="harga" class="form-label">Harga Perhari</label>
                                                <input value="<?= isset($_POST['harga'])?$_POST['harga']:number_format($k['harga_sewa_perhari'], 0, ',', '.'); ?>" type="text" class="form-control <?= (isset($errEdit['harga']) ? "is-invalid" : ""); ?>" name="harga" id="harga" placeholder="0">
                                                <span class="text-danger"><?= isset($errEdit['harga'])?$errEdit['harga']:""; ?></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="stok" class="form-label">Stok</label>
                                                <input value="<?= isset($_POST['stok'])?$_POST['stok']:$k['stok']; ?>" type="text" class="form-control <?= (isset($errEdit['stok']) ? "is-invalid" : ""); ?>" name="stok" id="stok" placeholder="0">
                                                <span class="text-danger"><?= isset($errEdit['stok'])?$errEdit['stok']:""; ?></span>
                                            </div>
                                            <h5>Spesifikasi</h5>
                                            <div class="mb-3">
                                                <label for="resolusi" class="form-label">Resolusi</label>
                                                <input value="<?= isset($_POST['resolusi'])?$_POST['resolusi']:$k['resolusi']; ?>" type="Text" class="form-control <?= (isset($errEdit['resolusi']) ? "is-invalid" : ""); ?>" name="resolusi" id="resolusi" placeholder="Resolusi">
                                                <span class="text-danger"><?= isset($errEdit['resolusi'])?$errEdit['resolusi']:""; ?></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="sensor" class="form-label">Sensor</label>
                                                <input value="<?= isset($_POST['sensor'])?$_POST['sensor']:$k['sensor']; ?>" type="Text" class="form-control <?= (isset($errEdit['sensor']) ? "is-invalid" : ""); ?>" name="sensor" id="sensor" placeholder="Sensor">
                                                <span class="text-danger"><?= isset($errEdit['sensor'])?$errEdit['sensor']:""; ?></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="iso_max" class="form-label">Iso Max</label>
                                                <input value="<?= isset($_POST['iso_max'])?$_POST['iso_max']:$k['iso_max']; ?>" type="Text" class="form-control <?= (isset($errEdit['iso_max']) ? "is-invalid" : ""); ?>" name="iso_max" id="iso_max" placeholder="Iso Max">
                                                <span class="text-danger"><?= isset($errEdit['iso_max'])?$errEdit['iso_max']:""; ?></span>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary" value="<?= $k['kamera_id']; ?>" type="submit" name="editKamera">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Hapus -->
                        <div class="modal fade text-gray-900" id="modalHapus<?= $k['kamera_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Hapus Data</h5>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form action="" method="post">
                                        <div class="modal-body">
                                            <p>Hapus</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-danger" value="<?= $k['kamera_id']; ?>" type="submit" name="deleteKamera">Hapus</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php }} ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade text-gray-900" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar</label>
                        <input type="file" class="form-control" name="gambar" id="gambar">
                        <span class="text-danger"><?= isset($errAdd['gambar'])?$errAdd['gambar']:""; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="merk" class="form-label">Merk</label>
                        <input value="<?= isset($_POST['merk'])?$_POST['merk']:''; ?>" type="text" class="form-control <?= (isset($errAdd['merk']) ? "is-invalid" : ""); ?>" name="merk" id="merk" placeholder="Merk">
                        <span class="text-danger"><?= isset($errAdd['merk'])?$errAdd['merk']:""; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="harga" class="form-label">Harga Perhari</label>
                        <input value="<?= isset($_POST['harga'])?$_POST['harga']:''; ?>" type="text" class="form-control <?= (isset($errAdd['harga']) ? "is-invalid" : ""); ?>" name="harga" id="harga" placeholder="0">
                        <span class="text-danger"><?= isset($errAdd['harga'])?$errAdd['harga']:""; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input value="<?= isset($_POST['stok'])?$_POST['stok']:''; ?>" type="text" class="form-control <?= (isset($errAdd['stok']) ? "is-invalid" : ""); ?>" name="stok" id="stok" placeholder="0">
                        <span class="text-danger"><?= isset($errAdd['stok'])?$errAdd['stok']:""; ?></span>
                    </div>
                    <h5>Spesifikasi</h5>
                    <div class="mb-3">
                        <label for="resolusi" class="form-label">Resolusi</label>
                        <input value="<?= isset($_POST['resolusi'])?$_POST['resolusi']:''; ?>" type="Text" class="form-control <?= (isset($errAdd['resolusi']) ? "is-invalid" : ""); ?>" name="resolusi" id="resolusi" placeholder="Resolusi">
                        <span class="text-danger"><?= isset($errAdd['resolusi'])?$errAdd['resolusi']:""; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="sensor" class="form-label">Sensor</label>
                        <input value="<?= isset($_POST['sensor'])?$_POST['sensor']:''; ?>" type="Text" class="form-control <?= (isset($errAdd['sensor']) ? "is-invalid" : ""); ?>" name="sensor" id="sensor" placeholder="Sensor">
                        <span class="text-danger"><?= isset($errAdd['sensor'])?$errAdd['sensor']:""; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="iso_max" class="form-label">Iso Max</label>
                        <input value="<?= isset($_POST['iso_max'])?$_POST['iso_max']:''; ?>" type="Text" class="form-control <?= (isset($errAdd['iso_max']) ? "is-invalid" : ""); ?>" name="iso_max" id="iso_max" placeholder="Iso Max">
                        <span class="text-danger"><?= isset($errAdd['iso_max'])?$errAdd['iso_max']:""; ?></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit" name="addKamera">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>