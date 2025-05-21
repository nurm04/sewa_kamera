<?php 
    include './koneksi.php';
    include './crud/kamera.php';
    include './crud/transaksi.php';
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sewa Kamera</title>
        <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="admin/css/sb-admin-2.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    </head>
    <body style="background: #E0E0E0;">
        <nav class="navbar navbar-expand-lg bg-dark sticky-top" data-bs-theme="light" style="z-index: 50; border-bottom: 3px solid blue;">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center justify-content-center" href="home">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="text-secondary fas fa-camera-retro"></i>
                    </div>
                    <div class="sidebar-brand-text fw-bold text-light">Sewa <span class="text-primary">Kamera</span></div>
                </a>
                <form class="d-none d-sm-inline-block form-inline navbar-search" style="width: 500px;" action="" method="post">
                    <div class="input-group">
                        <input name="cari" type="text" class="form-control bg-light border-2 border-primary small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button name="cariin" class="btn btn-primary" type="submit">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
                <?php if (!isset($_SESSION['user'])) {?>
                    <div class="d-flex align-items-center gap-3">
                        <a class="nav-link text-light" href="signin.php">Sign In</a>
                        <div class="vr bg-light" style="height: 20px;"></div>
                        <a class="nav-link text-light" href="signup.php">Sign Up</a>
                    </div>
                <?php } else { ?>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center text-light" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="me-2 d-none d-lg-inline"><?= $_SESSION['user']['nama']; ?></span>
                                <img src="img/undraw_profile.svg" alt="profile" class="rounded-circle" width="32" height="32">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item  text-dark" href="logout.php" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                <?php } ?>
            </div>
        </nav>
        <nav class="navbar navbar-expand-lg bg-light shadow" style="height: 35px;">
            <div class="container">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kamera">Kamera</a>
                    </li>
                    <?php if (isset($_SESSION['user'])) {?>
                    <li class="nav-item">
                        <a class="nav-link" href="riwayat-sewa">Riwayat Sewa</a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="panduan-sewa">Panduan Sewa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="faq">Kontak / Bantuan</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container">
            <?php 
                if (isset($_GET['x'])) {
                    if ($_GET['x'] == "home") {
                        include 'home.php';

                    } elseif ($_GET['x'] == "kamera") {
                        include 'kamera.php';
                    } elseif ($_GET['x'] == "detail") {
                        include 'detail.php';
                    } elseif ($_GET['x'] == "riwayat-sewa") {
                        include 'riwayat_sewa.php';
                    } elseif ($_GET['x'] == "panduan-sewa") {
                        include 'panduan_sewa.php';
                    } elseif ($_GET['x'] == "faq") {
                        include 'faq.php';
                    }
                } else {
                    include 'home.php';
                }
            ?>
        </div>
        <div class="mt-5 p-3 pb-0 bg-dark" style="width: 100%; border-top: 3px solid blue;">
            <div class="container">                
                <div class="row text-light">
                    <div class="col-4 d-flex justify-content-center">
                        <div class="mt-4">
                            <a class="navbar-brand d-flex align-items-center justify-content-center fs-1" href="home">
                                <div class="sidebar-brand-icon rotate-n-15">
                                    <i class="text-secondary fas fa-camera-retro"></i>
                                </div>
                                <div class="sidebar-brand-text fw-bold">Sewa <span class="text-primary">Kamera</span></div>
                            </a>
                        </div>
                    </div>
                    <div class="col-4 border-md-start border-light ps-5 pt-4">
                        <h5>Kontak</h5>
                        <a class="text-decoration-none text-light"><i class="fas fa-envelope me-3"></i>asd@gmail.com</a><br>
                        <a class="text-decoration-none text-light"><i class="fas fa-phone me-3"></i>000-00000000</a><br>
                    </div>
                    <div class="col-4 border-md-start border-light ps-5 pt-4">
                        <h5>Alamat</h5>
                        <p>Jl. Raya Telang, Kecamatan Kamal, Bangkalan Jawa Timur 69162 Indonesia</p>
                    </div>
                </div>
            </div>
            <div class="text-light text-center py-3 mt-5 border-top border-secondary" style="width: 100%;">
                <p>2025 Hak Cipta</p>
            </div>
        </div>
        
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Ready to Leave?</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
        <script src="admin/vendor/jquery/jquery.min.js"></script>
        <script src="admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="admin/vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="admin/js/sb-admin-2.min.js"></script>

        <!-- Page level plugins -->
        <script src="admin/vendor/chart.js/Chart.min.js"></script>

        <!-- Page level custom scripts -->
        <script src="admin/js/demo/chart-area-demo.js"></script>
        <script src="admin/js/demo/chart-pie-demo.js"></script>
    </body>
</html>