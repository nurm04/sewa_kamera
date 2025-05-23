<?php 
    include './../crud/penyewa.php';
    
    $errnama = "";
    $errno_tlp = "";
    $erremail = "";
    $errpassword = "";
    $errupload = "";

    if (isset($_POST['addPenyewa'])) {
        $nama = $_POST['nama'];
        $no_tlp = $_POST['no_tlp'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $upload_identitas = $_FILES['upload_identitas']['name'];
    
        $result = addPenyewa($nama, $no_tlp, $email, $password, $upload_identitas);
 
        if ($result === true) {
            alertSucces('Tambah data berhasil.','data-penyewa');
        } else if ($result === false) {
            alertDanger('Tambah data gagal.!');
        } else {
            alertDanger('Tambah data gagal, silahkan cek form tadi!');
            $errnama = $result['nama'] ?? "";
            $errno_tlp = $result['no_tlp'] ?? "";
            $erremail = $result['email'] ?? "";
            $errpassword = $result['password'] ?? "";
            $errupload = $result['upload_identitas'] ?? "";
        }
    }
    
    if (isset($_POST['deletePenyewa'])) {
        $penyewa_id = $_POST['penyewa_id'];

        $result = deletePenyewa($penyewa_id);
    
        if ($result === true) {
            alertSucces('Ubah data berhasil.','data-penyewa');
        } else {
            alertDanger($result);
        }
    }
?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center mb-3 mt-1 flex-wrap">
        <h1 class="h3 text-gray-900 mb-0">Data Penyewa</h1>
        <form class="form-inline my-2 my-md-0" action="" method="post">
            <div class="input-group">
                <input type="text" name="cari" class="form-control bg-light border-2 border-primary small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" style="width: 300px;">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit" name="cariin">
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
                <h6 class="m-0 font-weight-bold text-primary">Data Penyewa</h6>
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">+ Tambah Data</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-gray-900" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>No Handphone</th>
                            <th>Email</th>
                            <th>Foto</th>
                            <th class="text-center">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            if(mysqli_num_rows($getAllPenyewa) > 0) { 
                                foreach ($getAllPenyewa as $key => $p) { 
                        ?>
                        <tr>
                            <td><?= $key+1; ?></td>
                            <td><?= $p['nama']; ?></td>
                            <td><?= $p['no_tlp']; ?></td>
                            <td><?= $p['email']; ?></td>
                            <td>
                                <img src="./../img/<?= $p['upload_identitas']; ?>" alt="<?= $p['upload_identitas']; ?>" height="70">
                            </td>
                            <td>
                                <button data-toggle="modal" data-target="#modalHapus<?= $p['penyewa_id']; ?>" class="btn btn-danger" style="width: 37px; padding-left: 10px;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>

                        <!-- Modal Hapus -->
                        <div class="modal fade text-gray-900" id="modalHapus<?= $p['penyewa_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                            <input type="hidden" name="penyewa_id" value="<?= $p['penyewa_id']; ?>">
                                            <p>Hapus</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-danger" type="submit" name="deletePenyewa">Hapus</button>
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
                        <label for="nama" class="form-label">Nama</label>
                        <input value="<?= isset($_POST['nama'])?$_POST['nama']:''; ?>" type="text" class="form-control <?= ($errnama !="" ? "is-invalid" : ""); ?>" name="nama" id="nama" placeholder="nama">
                        <span class="text-danger"><?= $errnama; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="no_tlp" class="form-label">No Handphone</label>
                        <input value="<?= isset($_POST['no_tlp'])?$_POST['no_tlp']:''; ?>" type="text" class="form-control <?= ($errno_tlp !="" ? "is-invalid" : ""); ?>" name="no_tlp" id="no_tlp" placeholder="08">
                        <span class="text-danger"><?= $errno_tlp; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input value="<?= isset($_POST['email'])?$_POST['email']:''; ?>" type="email" class="form-control <?= ($erremail !="" ? "is-invalid" : ""); ?>" name="email" id="email" placeholder="asd@gmail.com">
                        <span class="text-danger"><?= $erremail; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input value="<?= isset($_POST['password'])?$_POST['password']:''; ?>" type="password" class="form-control <?= ($errpassword !="" ? "is-invalid" : ""); ?>" name="password" id="password" placeholder="***">
                        <span class="text-danger"><?= $errpassword; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="upload_identitas" class="form-label">Upload Identitas</label>
                        <input value="<?= isset($_POST['upload_identitas'])?$_POST['upload_identitas']:''; ?>" type="file" class="form-control <?= ($errupload !="" ? "is-invalid" : ""); ?>" name="upload_identitas" id="upload_identitas">
                        <span class="text-danger"><?= $errupload; ?></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit" name="addPenyewa">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>