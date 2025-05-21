<?php 
    $id = $_GET['id'];
    $result = getByIdKamera($id);
    $r = $result['data'];
    $errAdd = [];

    if (isset($_POST['addTransaksi'])) {
        if (isset($_SESSION['user'])) {
            $pinjam = $_POST['pinjam'];
            $kembali = $_POST['kembali'];
            $total = $_POST['total'];
            $jumlah = $_POST['jumlah'];
            $metode = $_POST['metode'];
    
            $result = addTransaksi($_SESSION['user']['penyewa_id'], $id, $pinjam, $kembali, $total, $jumlah, $metode);
            
            if ($result['status'] === true) {
                alertSucces('Tambah data berhasil.','riwayat-sewa');
            } else if ($result['status'] === false) {
                alertDanger($result['data']);
            } else {
                alertDanger('Ubah data gagal, silahkan cek form tadi!');
                $errAdd = $result;
            }
            
        } else {
            alertWarning('Silahkan login dulu','signin.php');
        }
    }
?>
<div class="row mt-4">
    <div class="col-7">
        <div class="gambar d-flex w-100 overflow-hidden justify-content-center" style="height: 300px; background-color:rgb(0, 0, 0, 0.25);">
            <img src="./img/<?= $r['gambar']; ?>" alt="<?= $r['gambar']; ?>" class="h-100">
        </div>

        <div class="card mt-4">
            <div class="card-header" style="font-size: 30px; font-weight: 600;">
                Spesifikasi
            </div>
            <div class="card-body">
                <table>
                    <tr>
                        <td>Resolusi</td>
                        <td>: <?= $r['resolusi']; ?></td>
                    </tr>
                    <tr>
                        <td>Sensor</td>
                        <td>: <?= $r['sensor']; ?></td>
                    </tr>
                    <tr>
                        <td>Iso Max</td>
                        <td>: <?= $r['iso_max']; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-5">
        <div class="card">
            <div class="card-header text-center" style="font-size: 20px; font-weight: 600;">
                <?= $r['merk']; ?>
            </div>
            <div class="card-body text-center">
                <h5 class="card-title" id="hargaPerhari">Rp. <?= number_format($r['harga_sewa_perhari'], 0, ',', '.'); ?> / hari</h5>
            </div>
            <div class="card-footer">
                <div class="accordion-item">
                    <h2 class="accordion-header text-center">
                        <button class="btn btn-primary px-4" type="button" data-bs-toggle="collapse" data-bs-target="#transaksi" aria-expanded="false" aria-controls="transaksi">
                            Sewa
                        </button>
                    </h2>
                    <div id="transaksi" class="accordion-collapse collapse">
                        <div class="accordion-body">
                            <form action="" method="post">
                                <div class="mb-3">
                                    <label for="pinjam" class="form-label">Tanggal Sewa</label>
                                    <input type="date" class="form-control <?= isset($errAdd['pinjam']) ? "is-invalid" : ""; ?> border-dark" id="pinjam" name="pinjam" value="<?= isset($_POST['pinjam']) ? $_POST['pinjam'] : ''; ?>">
                                    <span class="text-danger"><?= isset($errAdd['pinjam']) ? $errAdd['pinjam'] : ''; ?></span>
                                </div>

                                <div class="mb-3">
                                    <label for="kembali" class="form-label">Tanggal Kembali</label>
                                    <input type="date" class="form-control <?= isset($errAdd['kembali']) ? "is-invalid" : ""; ?> border-dark" id="kembali" name="kembali" value="<?= isset($_POST['kembali']) ? $_POST['kembali'] : ''; ?>">
                                    <span class="text-danger"><?= isset($errAdd['kembali']) ? $errAdd['kembali'] : ''; ?></span>
                                </div>

                                <div class="mb-3">
                                    <label for="jumlah" class="form-label">Jumlah Barang:</label>
                                    <div class="d-flex justify-content-start align-items-center gap-2">
                                        <button type="button" class="btn btn-primary" onclick="ubahJumlah(-1)">-</button>
                                        <input type="text" class="form-control text-center <?= isset($errAdd['jumlah']) ? "is-invalid" : ""; ?> border-dark" id="jumlah-barang" name="jumlah" value="<?= isset($_POST['jumlah']) ? $_POST['jumlah'] : '1'; ?>" readonly style="width: 50px;">
                                        <button type="button" class="btn btn-primary" onclick="ubahJumlah(1)">+</button>
                                    </div>
                                    <span class="text-danger"><?= isset($errAdd['jumlah']) ? $errAdd['jumlah'] : ''; ?></span>
                                </div>

                                <div class="mb-3">
                                    <label for="metode" class="form-label">Metode Pembayaran:</label>
                                    <select class="form-select <?= isset($errAdd['metode']) ? "is-invalid" : ""; ?>" name="metode" id="metode">
                                        <option value="">---</option>
                                        <option value="COD" <?= (isset($_POST['metode']) && $_POST['metode'] == 'COD') ? 'selected' : ''; ?>>COD</option>
                                        <option value="Transfer" <?= (isset($_POST['metode']) && $_POST['metode'] == 'Transfer') ? 'selected' : ''; ?>>Transfer</option>
                                    </select>
                                    <span class="text-danger"><?= isset($errAdd['metode']) ? $errAdd['metode'] : ''; ?></span>
                                </div>

                                <div class="mb-3 d-flex align-items-center">
                                    <label for="total" class="form-label" style="width: 65px;">Total:</label>
                                    <input type="hidden" name="harga" value="<?= number_format($r['harga_sewa_perhari'], 0, '', ''); ?>">
                                    <input type="text" class="form-control border-dark" placeholder="Rp. 150.000" id="total" name="total" value="<?= isset($_POST['total']) ? $_POST['total'] : ''; ?>" readonly>
                                </div>

                                <button class="btn btn-primary px-4" type="submit" name="addTransaksi">Deal</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<p style="font-size: 30px; font-weight: 600;" class="mt-3">Kamera Lainnya</p>
<div class="row row-cols-4">
    <?php 
        if(mysqli_num_rows($getAllKamera) > 0) { 
            $no = 1;
            foreach ($getAllKamera as $key => $p) {
                if ($p['kamera_id'] !== $id) {
    ?>
    <div class="col mb-4">
        <div class="card produk" style="width: 16rem;">
            <img src="./img/<?= $p['gambar']; ?>" class="card-img-top" alt="<?= $p['gambar']; ?>" style="height: 200px;">
            <div class="card-body">
                <h6 class="card-title"><?= $p['merk']; ?></h6>
                <p class="card-text">
                    <span class="text-primary fw-bold fs-5">Rp. <?= number_format($p['harga_sewa_perhari'], 0, ',', '.'); ?></span>
                    /hari
                </p>
                <a href="detail?id=<?= $p['kamera_id']; ?>" class="btn btn-dark">Detail</a>
            </div>
        </div>
    </div>
    <?php }}} ?>
</div>
<script>
    function hitungTotal() {
        const hargaPerHari = parseInt(document.getElementById('hargaPerhari').textContent.replace(/[^0-9]/g, ''));
        const jumlahBarang = parseInt(document.getElementById('jumlah-barang').value);
        const tanggalPinjam = document.getElementById('pinjam').value;
        const tanggalKembali = document.getElementById('kembali').value;

        if (tanggalPinjam && tanggalKembali) {
            const tgl1 = new Date(tanggalPinjam);
            const tgl2 = new Date(tanggalKembali);
            const selisih = Math.ceil((tgl2 - tgl1) / (1000 * 60 * 60 * 24)); // selisih hari

            if (selisih >= 1) {
                const total = hargaPerHari * jumlahBarang * selisih;
                document.getElementById('total').value = `Rp. ${total.toLocaleString('id-ID')}`;
            } else {
                document.getElementById('total').value = "Tanggal kembali harus setelah tanggal pinjam";
            }
        } else {
            document.getElementById('total').value = "";
        }
    }

    function ubahJumlah(jumlah) {
        const input = document.getElementById('jumlah-barang');
        let nilai = parseInt(input.value);
        nilai += jumlah;
        if (nilai < 1) nilai = 1;
        input.value = nilai;
        hitungTotal();
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('pinjam').addEventListener('change', hitungTotal);
        document.getElementById('kembali').addEventListener('change', hitungTotal);
        document.getElementById('jumlah-barang').addEventListener('input', hitungTotal);
    });
</script>
