<?php include './../koneksi.php' ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Admin</title>
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="css/sb-admin-2.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    </head>

    <body id="page-top">
        <div id="wrapper">
            <ul class="navbar-nav bg-primary sidebar sidebar-dark accordion" id="accordionSidebar">
                <a class="sidebar-brand d-flex align-items-center justify-content-center bg-dark" href="dashboard">
                    <div class="sidebar-brand-icon rotate-n-15">
                        <i class="fas fa-camera-retro text-secondary"></i>
                    </div>
                    <div class="sidebar-brand-text">Sewa <span class="text-primary">Kamera</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <li class="nav-item">
                    <a class="nav-link fs-5 fw-bold text-light" href="dashboard">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-5 fw-bold text-light" href="data-penyewa">
                        <i class="fas fa-fw fa-user"></i>
                        <span>Data Penyewa</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-5 fw-bold text-light" href="data-kamera">
                        <i class="fas fa-fw fa-camera"></i>
                        <span>Data Kamera</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fs-5 fw-bold text-light" href="data-transaksi">
                        <i class="fas fa-fw fa-table"></i>
                        <span>Data Transaksi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link collapsed text-light fs-5 fw-bold" href="#" data-toggle="collapse" data-target="#collapseTwo"
                        aria-expanded="true" aria-controls="collapseTwo">
                        <i class="fas fa-fw fa-cog"></i>
                        <span>Data Riwayat</span>
                    </a>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <a class="collapse-item" href="riwayat-kamera">Kamera</a>
                            <a class="collapse-item" href="riwayat-denda">Denda</a>
                        </div>
                    </div>
                </li>
                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">
                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>
            </ul>
            <!-- End of Sidebar -->

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <nav class="navbar navbar-expand navbar-light bg-light topbar mb-4 static-top shadow">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="navbar-nav ml-auto">
                            <div class="topbar-divider d-none d-sm-block"></div>
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-white small">Admin</span>
                                    <img class="img-profile rounded-circle" src="./../img/undraw_profile.svg">
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                    aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        Logout
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>
                    <!-- End of Topbar -->
                    <?php 
                        if (isset($_GET['x'])) {
                            if ($_GET['x'] == "dashboard") {
                                include 'dashboard.php';

                            } elseif ($_GET['x'] == "data-penyewa") {
                                include 'data_penyewa.php';
                            } elseif ($_GET['x'] == "data-kamera") {
                                include 'data_kamera.php';
                            } elseif ($_GET['x'] == "data-transaksi") {
                                include 'data_transaksi.php';
                            } elseif ($_GET['x'] == "riwayat-kamera") {
                                include 'riwayat_kamera.php';
                            } elseif ($_GET['x'] == "riwayat-denda") {
                                include 'riwayat_denda.php';
                            }
                        } else {
                            include 'dashboard.php';
                        }
                    ?>
                    
                </div>
                <!-- End of Main Content -->

                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; Sewa Kamera 2021</span>
                        </div>
                    </div>
                </footer>
                <!-- End of Footer -->
            </div>
            <!-- End of Content Wrapper -->
        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="./../logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap core JavaScript-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="js/sb-admin-2.min.js"></script>

        <!-- Page level plugins -->
        <script src="vendor/chart.js/Chart.min.js"></script>

        <!-- Page level custom scripts -->
        <script src="js/demo/chart-area-demo.js"></script>
        <script src="js/demo/chart-pie-demo.js"></script>

    </body>
</html>
<style>
.sidebar {
  position: fixed;
  top: 0px;
  bottom: 0;
  left: 0;
  z-index: 1030;
  height: calc(100vh - 56px);
  overflow-y: auto;
}
.navbar {
  position: fixed;
  top: 0;
  right: 0;
  width: calc(100% - 224px);
  z-index: 1040;
  transition: width ease;
} 

.sidebar.toggled ~ #content-wrapper .navbar {
  width: calc(100% - 104px);
}
#content-wrapper {
  margin-left: 224px;
  margin-top: 80px;
}

.sidebar.toggled + #content-wrapper {
  margin-left: 104px;
}

@media (max-width: 768px) {
  #content-wrapper {
    margin-left: 0;
  }
  .navbar {
    width: 100% !important;
  }
}

</style>