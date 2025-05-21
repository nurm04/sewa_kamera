<?php 
    // Create
    function addPenyewa($nama, $no_tlp, $email, $password, $upload_identitas) {
        global $koneksi;
        $errors = [];
    
        if (empty($nama)) {
            $errors['nama'] = "* Nama tidak boleh kosong.";
        }
    
        if (empty($no_tlp)) {
            $errors['no_tlp'] = "* No Handphone tidak boleh kosong.";
        } elseif (!preg_match("/^[0-9]{10,15}$/", $no_tlp)) {
            $errors['no_tlp'] = "* No Handphone tidak valid.";
        }
    
        if (empty($email)) {
            $errors['email'] = "* Email tidak boleh kosong.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "* Format email tidak valid.";
        } elseif (mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM penyewa WHERE email = '$email'")) > 0) {
            $errors['email'] = "* Email sudah ada.";
        } 
        
    
        if (empty($password)) {
            $errors['password'] = "* Password tidak boleh kosong.";
        }
    
        if (empty($upload_identitas)) {
            $errors['upload_identitas'] = "* Identitas harus diunggah.";
        }
    
        if (!empty($errors)) {
            return $errors;
        }
    
        $query = mysqli_query($koneksi, "INSERT INTO penyewa VALUES ('', '$nama', '$no_tlp', '$email', '$password', '$upload_identitas')");
    
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    // Read
    $qGetAllPenyewa = "SELECT * FROM penyewa";
    $getAllPenyewa = mysqli_query($koneksi, $qGetAllPenyewa);

    function cekLogin($email, $password) {
        global $koneksi;

        $query = mysqli_query($koneksi, "SELECT * FROM penyewa WHERE email = '$email' AND password = '$password'");
        
        if (mysqli_num_rows($query) > 0) {
            return ["status" => true, "data" => mysqli_fetch_array($query)];
        } else {
            return ["status" => false];
        }
    }

    // Update

    // Delete
    function deletePenyewa($id) {
        global $koneksi;
    
        $query = mysqli_query($koneksi, "DELETE FROM penyewa WHERE penyewa_id = $id");
    
        if ($query) {
            return true;
        } else {
            return "Gagal menghapus ke database.";
        }
    }
?>