<div class="row">
    <div class="col-3">
        <p style="font-size: 30px; font-weight: 600;" class="mt-3">Merk</p>
        <div id="list-example" class="list-group">
            <form action="" method="post">
                <button type="submit" name="cari" value="" class="list-group-item list-group-item-action">Semua</button>
                <button type="submit" name="cari" value="Canon" class="list-group-item list-group-item-action">Canon</button>
                <button type="submit" name="cari" value="Fujifilm" class="list-group-item list-group-item-action">Fujifilm</button>
                <button type="submit" name="cari" value="Nikon" class="list-group-item list-group-item-action">Nikon</button>
                <button type="submit" name="cari" value="Panasonic" class="list-group-item list-group-item-action">Panasonic</button>
                <button type="submit" name="cari" value="Sony" class="list-group-item list-group-item-action">Sony</button>
            </form>
        </div>
    </div>
    <div class="col-9">
        <p style="font-size: 30px; font-weight: 600;" class="mt-3">Kamera</p>
        <div class="row row-cols-3">
            <?php 
                if(mysqli_num_rows($getAllKamera) > 0) { 
                    $no = 1;
                    foreach ($getAllKamera as $key => $p) { 
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
            <?php }} else { ?>
                <div class="alert alert-light text-center" role="alert">
                    Data tidak ada
                </div>
            <?php } ?>
        </div>
    </div>
</div>