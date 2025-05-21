<?php 
    include './../crud/kamera.php';
    if (isset($_POST['updateTransaksi'])) {
        $id = $_POST['updateTransaksi'];
        $data = mysqli_query($koneksi, "SELECT * FROM log_denda_update WHERE ldu_id = '$id'");
        $data = mysqli_fetch_array($data);

        $denda_id = $data['denda_id'];
        $jenis_denda = $data['jenis_denda_old'];
        $jumlah_denda = $data['jumlah_denda_old'];
        $keterangan = $data['keterangan_old'];

        $queryKamera = mysqli_query($koneksi, "UPDATE denda SET jenis_denda = '$jenis_denda', jumlah_denda = '$jumlah_denda', keterangan = '$keterangan' WHERE denda_id = '$denda_id'");
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
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Kamera</th>
                            <th>Penyewa</th>
                            <th>Status Denda</th>
                            <th class="text-center">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $query = mysqli_query($koneksi, "
                                SELECT 
                                ldu.*,
                                ds.detail_sewa_id, ds.tanggal_pinjam, ds.tanggal_kembali, ds.jumlah, ds.status,
                                t.transaksi_id, t.total, t.metode_pembayaran,
                                p.*,
                                k.*
                                FROM log_denda_update ldu
                                JOIN detail_sewa ds ON ldu.detail_sewa_id = ds.detail_sewa_id
                                JOIN transaksi t ON t.detail_sewa_id = ds.detail_sewa_id
                                JOIN penyewa p ON ds.penyewa_id = p.penyewa_id
                                JOIN kamera k ON ds.kamera_id = k.kamera_id
                            ");
                            if(mysqli_num_rows($query) > 0) { 
                                foreach ($query as $key => $t) {
                        ?>
                        <tr>
                            <td><?= $key+1; ?></td>
                            <td><?= $t['tanggal_pinjam']; ?></td>
                            <td><?= $t['tanggal_kembali']; ?></td>
                            <td><?= $t['merk']; ?></td>
                            <td><?= $t['nama']; ?></td>
                            <td><?= $t['jenis_denda_old'] == null ? "Tidak Ada" : "Terkena denda"; ?></td>
                            <td>
                                <button style="width: 37px;" type="button" class="btn btn-info" data-toggle="modal" data-target="#modalDetail<?= $t['detail_sewa_id']; ?>"><i class="fas fa-info"></i></button>
                                <button data-toggle="modal" data-target="#modalRestore<?= $t['ldu_id']; ?>" class="btn btn-warning" style="width: 37px; padding-left: 10px;"><i class="fas fa-arrow-alt-circle-left"></i></button>
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
                                        <p class="fw-bold">Perubahan data Denda</p>
                                        <p>Jenis Denda : <?= $t['jenis_denda_old']; ?> -> <?= $t['jenis_denda_new']; ?></p>
                                        <p>Jumlah Denda : Rp. <?= number_format($t['jumlah_denda_old'], 0, ',', '.'); ?> -> Rp. <?= number_format($t['jumlah_denda_new'], 0, ',', '.'); ?></p>
                                        <p>Keterangan : <?= $t['keterangan_old']; ?> -> <?= $t['keterangan_new']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal Restore -->
                        <div class="modal fade text-gray-900" id="modalRestore<?= $t['ldu_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                            <button class="btn btn-warning" value="<?= $t['ldu_id']; ?>" type="submit" name="updateTransaksi">Restore</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php }} else { ?>
                        <tr><td colspan="7"><center>Tidak ada data</center></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>