<?php 
    include './../crud/transaksi.php';
    if (isset($_POST['editTransaksi'])) {
        $id = $_POST['editTransaksi'];
        $jenis_denda = $_POST['jenis_denda'];
        $jumlah_denda = $_POST['jumlah_denda'];
        $keterangan = $_POST['keterangan'];
        $status = $_POST['status'];
    
        $result = editTransaksi($id, $jenis_denda, $jumlah_denda, $keterangan, $status);
    
        if ($result === true) {
            alertSucces('Ubah data berhasil.','data-transaksi');
        } else if ($result === false) {
            alertDanger('Ubah data gagal.!');
        } else {
            alertDanger('Ubah data gagal, silahkan cek form tadi!');
            $errEdit = $result;
        }
    }
?>
<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-900">Dashboard</h1>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Penghasilan Perbulan</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-gray-900" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Bulan</th>
                            <th>Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query = mysqli_query($koneksi, "SELECT * FROM view_pendapatan_per_bulan");
                            if(mysqli_num_rows($query) > 0) { 
                                foreach ($query as $key => $t) {
                        ?>
                        <tr>
                            <td><?= $key+1; ?></td>
                            <td><?= $t['bulan']; ?></td>
                            <td><?= $t['total_pendapatan']; ?></td>
                        </tr>
                        <?php }} else { ?>
                        <tr>
                            <td colspan="7"><center>Tidak ada data</center></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Kamera yang disewa</h6>
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
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-gray-900" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Kamera</th>
                            <th>Penyewa</th>
                            <th>Status Transaksi</th>
                            <th class="text-center">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query = mysqli_query($koneksi, "CALL sp_cek_denda_keterlambatan()");
                            if(mysqli_num_rows($query) > 0) { 
                                foreach ($query as $key => $t) {
                        ?>
                        <tr>
                            <td><?= $key+1; ?></td>
                            <td><?= $t['tanggal_pinjam']; ?></td>
                            <td><?= $t['tanggal_kembali']; ?></td>
                            <td><?= $t['merk']; ?></td>
                            <td><?= $t['nama']; ?></td>
                            <td>
                                <?= ($t['status'] == "disewa" && $t['denda_id'] != null) ? "Terlambat" : "Disewa"; ?>
                            </td>
                            <td>
                                <button style="width: 37px;" type="button" class="btn btn-info" data-toggle="modal" data-target="#modalDetail<?= $t['detail_sewa_id']; ?>"><i class="fas fa-info"></i></button>
                                <button style="width: 37px;padding-left: 10px;" type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalUpdate<?= $t['detail_sewa_id']; ?>"><i class="fas fa-pen"></i></button>
                            </td>
                        </tr>
                        <!-- Modal Detail -->
                        <div class="modal fade text-gray-900" id="modalDetail<?= $t['detail_sewa_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Details</h5>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tanggal Pinjam : <?= $t['tanggal_pinjam']; ?></p>
                                        <p>Tanggal Kembali : <?= $t['tanggal_kembali']; ?></p>
                                        <p>Merk Kamera : <?= $t['merk']; ?></p>
                                        <p>Jumlah : <?= $t['jumlah']; ?></p>
                                        <p>Total : Rp. <?= number_format($t['total'], 0, ',', '.'); ?></p>
                                        <p class="fw-bold">Data Penyewa </p>
                                        <p>Nama : <?= $t['nama']; ?></p>
                                        <p>Email : <?= $t['email']; ?></p>
                                        <p>No Handphone : <?= $t['no_tlp']; ?></p>
                                        <p class="fw-bold">Terkena Denda <?= $t['jenis_denda']; ?></p>
                                        <p>Jumlah Denda : Rp. <?= number_format($t['jumlah_denda'], 0, ',', '.'); ?></p>
                                        <p>Keterangan : <?= $t['keterangan']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Update -->
                        <div class="modal fade text-gray-900" id="modalUpdate<?= $t['detail_sewa_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Update Data</h5>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form action="" method="post">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Jenis Denda</label>
                                                <select name="status" id="status" class="form-control <?= (isset($errEdit['status']) ? "is-invalid" : ""); ?>">
                                                    <option value="<?= isset($_POST['status'])?$_POST['status']:$t['status']; ?>"><?= isset($_POST['status'])?$_POST['status']:$t['status']; ?></option>
                                                    <option value="disewa">Disewa</option>
                                                    <option value="selesai">Selesai</option>
                                                </select>
                                                <span class="text-danger"><?= isset($errEdit['status'])?$errEdit['status']:""; ?></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="jenis_denda" class="form-label">Jenis Denda</label>
                                                <select name="jenis_denda" id="jenis_denda" class="form-control <?= (isset($errEdit['jenis_denda']) ? "is-invalid" : ""); ?>">
                                                    <option value="<?= isset($_POST['jenis_denda'])?$_POST['jenis_denda']:$t['jenis_denda']; ?>"><?= isset($_POST['jenis_denda'])?$_POST['jenis_denda']:$t['jenis_denda']; ?></option>
                                                    <option value="terlambat">Terlambat</option>
                                                    <option value="kerusakan">Kerusakan</option>
                                                </select>
                                                <span class="text-danger"><?= isset($errEdit['jenis_denda'])?$errEdit['jenis_denda']:""; ?></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="jumlah_denda" class="form-label">Jumlah Denda</label>
                                                <input value="<?= isset($_POST['jumlah_denda'])?$_POST['jumlah_denda']:number_format($t['jumlah_denda'], 0, ',', '.'); ?>" type="text" class="form-control <?= (isset($errEdit['jumlah_denda']) ? "is-invalid" : ""); ?>" name="jumlah_denda" id="jumlah_denda" placeholder="Jumlah Denda">
                                                <span class="text-danger"><?= isset($errEdit['jumlah_denda'])?$errEdit['jumlah_denda']:""; ?></span>
                                            </div>
                                            <div class="mb-3">
                                                <label for="keterangan" class="form-label">Keterangan</label>
                                                <input value="<?= isset($_POST['keterangan'])?$_POST['keterangan']:$t['keterangan']; ?>" type="text" class="form-control <?= (isset($errEdit['keterangan']) ? "is-invalid" : ""); ?>" name="keterangan" id="keterangan" placeholder="Keterangan">
                                                <span class="text-danger"><?= isset($errEdit['keterangan'])?$errEdit['keterangan']:""; ?></span>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary" type="submit" value="<?= $t['detail_sewa_id']; ?>" name="editTransaksi">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php }} else { ?>
                        <tr>
                            <td colspan="7"><center>Tidak ada data</center></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>