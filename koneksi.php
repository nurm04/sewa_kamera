<?php 
    session_start();
    $koneksi = mysqli_connect("localhost", "root", "", "db_penyewaan_kamera");

    if ($koneksi->connect_error) die("Koneksi gagal: " . $koneksi->connect_error);

    function alertSucces($text, $href) {
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'Selamat!',
                    text: '$text',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href='$href'; 
                    }
                });
            }, 100);
        </script>";
    }

    function alertWarning($text, $href) {
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'Peringatan!',
                    text: '$text',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '$href'; 
                    }
                });
            }, 100);
        </script>";
    }

    function alertDanger($text) {
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    title: 'Gagal!',
                    text: '$text',
                    icon: 'error',
                    confirmButtonText: 'Coba Lagi'
                });
            }, 100);
        </script>";
    }
?>