<?php 
    include './koneksi.php';
    include './crud/penyewa.php';

    $errAdd = [];

    if (isset($_POST['addPenyewa'])) {
        $nama = $_POST['nama'];
        $no_tlp = $_POST['no_tlp'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $upload_identitas = "";
        if(!empty($_FILES['upload_identitas']['name'])) {
            $rand = rand();
            $filename = $_FILES['upload_identitas']['name'];
            $upload_identitas = $rand.'_'.$filename;
        } else {
            $upload_identitas = "";
        }
        move_uploaded_file($_FILES['upload_identitas']['tmp_name'], 'img/'.$upload_identitas);
    
        $result = addPenyewa($nama, $no_tlp, $email, $password, $upload_identitas);
    
        if ($result === true) {
            alertSucces('Register berhasil.','signin.php');
        } else {
            alertDanger('Register gagal, silahkan cek form tadi!');
            $errAdd = $result;
        }
    }
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sewa Kamera</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    </head>
    <body style="background: #E0E0E0;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6 sticky-top" style="background-image: url(img/bg.jpeg); height: 100vh; background-size: cover; background-position: center;"></div>
                <div class="col-6">
                    <div class="container">
                        <h1 class="m-5 fw-bold text-center">Sewa Kamera</h1>
                        <form class="m-5 d-flex flex-column" action="" method="post" enctype="multipart/form-data">
                            <p style="font-size: 30px; font-weight: 600;">Sign Up</p>
                            <div class="form-floating mb-3">
                                <input name="nama" value="<?= isset($_POST['nama'])?$_POST['nama']:''; ?>" type="text" class="form-control <?= isset($errAdd['nama']) ? "is-invalid" : ""; ?>" id="floatingInput" placeholder="Nama">
                                <label for="floatingInput">Nama</label>
                                <span class="text-danger"><?= isset($errAdd['nama'])?$errAdd['nama']:""; ?></span>
                            </div>
                            <div class="form-floating mb-3">
                                <input name="no_tlp" value="<?= isset($_POST['no_tlp'])?$_POST['no_tlp']:''; ?>" type="text" class="form-control <?= isset($errAdd['no_tlp']) ? "is-invalid" : ""; ?>" id="floatingInput" placeholder="No Handphone">
                                <label for="floatingInput">No Handphone</label>
                                <span class="text-danger"><?= isset($errAdd['no_tlp'])?$errAdd['no_tlp']:""; ?></span>
                            </div>
                            <div class="form-floating mb-3">
                                <input name="email" value="<?= isset($_POST['email'])?$_POST['email']:''; ?>" type="email" class="form-control <?= isset($errAdd['email']) ? "is-invalid" : ""; ?>" id="floatingInput" placeholder="Email">
                                <label for="floatingInput">Email</label>
                                <span class="text-danger"><?= isset($errAdd['email'])?$errAdd['email']:""; ?></span>
                            </div>
                            <div class="form-floating mb-3">
                                <input name="password" value="<?= isset($_POST['password'])?$_POST['password']:''; ?>" type="password" class="form-control <?= isset($errAdd['password']) ? "is-invalid" : ""; ?>" id="floatingInput" placeholder="Password">
                                <label for="floatingInput">Password</label>
                                <span class="text-danger"><?= isset($errAdd['password'])?$errAdd['password']:""; ?></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <label for="upload_identitas" class="form-label" style="width: 90px;">Foto Identitas:</label>
                                <input name="upload_identitas" id="upload_identitas" type="file" class="form-control <?= isset($errAdd['upload_identitas']) ? "is-invalid" : ""; ?>">
                            </div>
                            <span class="text-danger mb-3"><?= isset($errAdd['upload_identitas'])?$errAdd['upload_identitas']:""; ?></span>
                            <button class="btn btn-dark" type="submit" name="addPenyewa">Sign Up</button>
                        </form>
                        <p class="text-center mb-5">Sudah punya akun, silahkan <a class="" href="signin.php">Sign In</a></p>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    </body>
</html>