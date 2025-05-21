<?php 
    // Create
    function addTransaksi($penyewa_id, $kamera_id, $pinjam, $kembali, $total, $jumlah, $metode) {
        global $koneksi;
        $errors = [];
    
        if (empty($pinjam)) {
            $errors['pinjam'] = "* Tanggal pinjam tidak boleh kosong.";
        }
        if (empty($kembali)) {
            $errors['kembali'] = "* Tanggal kembali tidak boleh kosong.";
        }

        if (!empty($pinjam) && !empty($kembali)) {
            $tgl1 = strtotime($pinjam);
            $tgl2 = strtotime($kembali);
            if ($tgl2 <= $tgl1) {
                $errors['kembali'] = "* Tanggal kembali harus setelah tanggal pinjam.";
            }
        }
    
        $totalFinal = (int) str_replace(['Rp.', '.', ' '], '', $total);
        if (empty($totalFinal)) {
            $errors['total'] = "* total tidak boleh kosong.";
        } elseif (!is_numeric($totalFinal)) {
            $errors['total'] = "* total tidak valid.";
        }

        if (empty($jumlah) || $jumlah < 1) {
            $errors['jumlah'] = "* Jumlah hari tidak valid.";
        }
        
        if (empty($metode)) {
            $errors['metode'] = "* metode tidak boleh kosong.";
        }
    
        if (!empty($errors)) {
            return $errors;
        }

        $query = mysqli_query($koneksi, "CALL sp_simpan_transaksi('$penyewa_id', '$kamera_id', '$pinjam', '$kembali', '$jumlah', '$totalFinal', '$metode')");      
        if ($query) {
            return ["status" => true];
        } else {
            return ["status" => false, "data" => mysqli_error($koneksi)];
        }
    }

    // Read
    $qGetAllTransaksi = "SELECT * FROM view_all_data_transaksi";
    $getAllTransaksi = mysqli_query($koneksi, $qGetAllTransaksi);
    
    function getByIdUser($id) {
        global $koneksi;
        
        $qGetTransaksiByUser = "CALL sp_get_transaksi_by_user('$id')";
        $query = mysqli_query($koneksi, $qGetTransaksiByUser);
        
        if (mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return 0;
        }
    }

    // Update
    function editTransaksi($id, $jenis_denda, $jumlah_denda, $keterangan, $status) {
        global $koneksi;

        $dendaBersih = str_replace('.', '', $jumlah_denda);
        $dendaBersih = str_replace(',', '.', $dendaBersih);
        $dendaFinal = floatval($dendaBersih);
        
        $cari = mysqli_query($koneksi, "SELECT * FROM denda WHERE detail_sewa_id = '$id'");
        $udahStatus = mysqli_query($koneksi, "UPDATE detail_sewa SET status = '$status' WHERE detail_sewa_id = '$id'");
        if (mysqli_num_rows($cari) > 0) {
            mysqli_query($koneksi, "UPDATE denda SET jenis_denda = '$jenis_denda', jumlah_denda = '$dendaFinal', keterangan = '$keterangan' WHERE detail_sewa_id = '$id'");      
        } else {
            if (!empty($jenis_denda) && !empty($dendaFinal) && !empty($keterangan)) {
                mysqli_query($koneksi, "INSERT INTO denda VALUES ('', '$id', '$jenis_denda', '$dendaFinal', '$keterangan')");      
            }
        }
        
        if ($udahStatus) {
            return true;
        } else {
            return false;
        }
    }

    // Delete
    function deleteTransaksi($id) {
        global $koneksi;
    
        $query = mysqli_query($koneksi, "DELETE FROM denda WHERE detail_sewa_id = $id");
        $query .= mysqli_query($koneksi, "DELETE FROM transaksi WHERE detail_sewa_id = $id");
        $query .= mysqli_query($koneksi, "DELETE FROM detail_sewa WHERE detail_sewa_id = $id");
    
        if ($query) {
            return true;
        } else {
            return false;
        }
    }
?>