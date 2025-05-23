<div class="container-fluid mt-5">
    <!-- Page Heading -->
    <h1 class="h3 mb-2">Riwayat</h1>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Status Sewa</th>
                            <th>Tanggal Sewa</th>
                            <th>Kamera</th>
                            <th>Status Denda</th>
                            <th class="text-center">...</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $data = getByIdUser($_SESSION['user']['penyewa_id']);
                            if($data != null) {
                                foreach ($data as $key => $t) {
                        ?>
                        <tr>
                            <td><?= $key+1; ?></td>
                            <td><?= ($t['status'] == "disewa" && $t['jenis_denda'] == "terlambat" ? "Belum dikembalikan" : $t['status']); ?></td>
                            <td><?= $t['tanggal_pinjam']; ?> / <?= $t['tanggal_kembali']; ?></td>
                            <td><?= $t['merk']; ?></td>
                            <td><?= $t['jenis_denda'] == null ? "Tidak Ada" : $t['jenis_denda']; ?></td>
                            <td>
                                <button style="width: 37px;" type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $t['transaksi_id']; ?>"><i class="fas fa-info"></i></button>
                            </td>
                        </tr>
                        <div class="modal fade text-gray-900" id="modalDetail<?= $t['transaksi_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Detail</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Tanggal Pinjam : <?= $t['tanggal_pinjam']; ?></p>
                                        <p>Tanggal Kembali : <?= $t['tanggal_kembali']; ?></p>
                                        <p>Merk Kamera : <?= $t['merk']; ?></p>
                                        <p>Jumlah : <?= $t['jumlah']; ?></p>
                                        <p>Total : Rp. <?= number_format($t['total'], 0, ',', '.'); ?></p>
                                        <p class="fw-bold">Terkena Denda <?= $t['jenis_denda']; ?></p>
                                        <p>Jumlah Denda : Rp. <?= number_format($t['jumlah_denda'], 0, ',', '.'); ?></p>
                                        <p>Keterangan : <?= $t['keterangan']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }} else { ?>
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>