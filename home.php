<div id="carouselExampleIndicators" class="carousel slide mt-3" style="background-color:rgb(135, 135, 135);">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="./img/Banner1.png" class="d-block w-100" alt="" style="height: 300px;">
        </div>
        <div class="carousel-item">
            <img src="./img/Banner2.jpg" class="d-block w-100" alt="" style="height: 300px;">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<p style="font-size: 30px; font-weight: 600;" class="mt-3">Kamera Terlaris</p>
<div class="row row-cols-4">
    <?php 
        $query = mysqli_query($koneksi, "SELECT * FROM view_total_sewa_per_kamera LIMIT 4");
        foreach ($query as $p) {
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
                <p class="card-text">Disewa: <?= $p['jumlah_sewa']; ?></p>
                <a href="detail?id=<?= $p['kamera_id']; ?>" class="btn btn-dark">Detail</a>
            </div>
        </div>
    </div>
    <?php } ?>
</div>