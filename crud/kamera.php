<?php 
    // Create
    function addKamera($merk, $harga, $stok, $gambar, $resolusi, $sensor, $iso_max) {
        global $koneksi;
        $errors = [];
    
        if (empty($merk)) {
            $errors['merk'] = "* Merk tidak boleh kosong.";
        }
    
        $hargaBersih = str_replace('.', '', $harga);
        $hargaBersih = str_replace(',', '.', $hargaBersih);
        $hargaFinal = floatval($hargaBersih);
        if (empty($hargaFinal)) {
            $errors['harga'] = "* Harga tidak boleh kosong.";
        } elseif (!preg_match("/^[0-9]+$/", $hargaFinal)) {
            $errors['harga'] = "* Harga tidak valid.";
        }

        if (empty($stok)) {
            $errors['stok'] = "* Stok tidak boleh kosong.";
        } elseif (!preg_match("/^[0-9]+$/", $stok)) {
            $errors['stok'] = "* Stok tidak valid.";
        }
        
        if (empty($gambar)) {
            $errors['stok'] = "* Gambar tidak boleh kosong.";
        }

        if (empty($resolusi)) {
            $errors['resolusi'] = "* Resolusi tidak boleh kosong.";
        }
    
        if (empty($sensor)) {
            $errors['sensor'] = "* Sensor tidak boleh kosong.";
        }
    
        if (empty($iso_max)) {
            $errors['iso_max'] = "* Iso Max harus diunggah.";
        }
    
        if (!empty($errors)) {
            return $errors;
        }
    
        $query = mysqli_query($koneksi, "INSERT INTO kamera VALUES ('', '$merk', '$hargaFinal', '$stok', '$gambar')");      
        if ($query) {
            $idtadi = mysqli_insert_id($koneksi);
            $querylagi = mysqli_query($koneksi, "INSERT INTO spesifikasi VALUES ('', '$idtadi', '$resolusi', '$sensor', '$iso_max')");
        }

        if ($querylagi) {
            return true;
        } else {
            return "Gagal menyimpan ke database.";
        }
    }

    // Read
    $qGetAllKamera = "";
    if (isset($_POST['cariin']) || isset($_POST['cari'])) {
        $keyworK = $_POST['cari'];
    }

    if (!empty($keyworK)) {
        $qGetAllKamera = "CALL sp_search_kamera('$keyworK')";
    } else {
        $qGetAllKamera = "SELECT * FROM view_all_data_kamera";
    }

    $getAllKamera = mysqli_query($koneksi, $qGetAllKamera);
    mysqli_next_result($koneksi);

    function getByIdKamera($id) {
        global $koneksi;
        $query = mysqli_query($koneksi, "SELECT * FROM view_all_data_kamera WHERE kamera_id = $id");
        
        if (mysqli_num_rows($query) > 0) {
            return ["status" => true, "data" => mysqli_fetch_array($query)];
        } else {
            return ["status" => false];
        }
    }

    // Update
    function editKamera($id, $merk, $harga, $stok, $gambar, $resolusi, $sensor, $iso_max) {
        global $koneksi;
        $errors = [];
    
        if (empty($merk)) {
            $errors['merk'] = "* Merk tidak boleh kosong.";
        }
    
        $hargaBersih = str_replace('.', '', $harga);
        $hargaBersih = str_replace(',', '.', $hargaBersih);
        $hargaFinal = floatval($hargaBersih);
        if (empty($hargaFinal)) {
            $errors['harga'] = "* Harga tidak boleh kosong.";
        } elseif (!preg_match("/^[0-9]+$/", $hargaFinal)) {
            $errors['harga'] = "* Harga tidak valid.";
        }

        if (empty($stok)) {
            $errors['stok'] = "* Stok tidak boleh kosong.";
        } elseif (!preg_match("/^[0-9]+$/", $stok)) {
            $errors['stok'] = "* Stok tidak valid.";
        }

        if (empty($gambar)) {
            $errors['stok'] = "* Gambar tidak boleh kosong.";
        }
    
        if (empty($resolusi)) {
            $errors['resolusi'] = "* Resolusi tidak boleh kosong.";
        }
    
        if (empty($sensor)) {
            $errors['sensor'] = "* Sensor tidak boleh kosong.";
        }
    
        if (empty($iso_max)) {
            $errors['iso_max'] = "* Iso Max harus diunggah.";
        }
    
        if (!empty($errors)) {
            return $errors;
        }
        
        $query = mysqli_query($koneksi, "CALL sp_update_kamera('$id','$merk','$hargaFinal','$stok','$gambar','$resolusi','$sensor','$iso_max')");

        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    // Delete
    function deleteKamera($id) {
        global $koneksi;
    
        $query = mysqli_query($koneksi, "DELETE FROM spesifikasi WHERE kamera_id = $id");
        $query .= mysqli_query($koneksi, "DELETE FROM kamera WHERE kamera_id = $id");
    
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
?>