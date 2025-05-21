-- Storage Procedure
-- 1
DELIMITER $$
CREATE PROCEDURE sp_get_transaksi_by_user(IN in_penyewa_id INT)
BEGIN
    SELECT 
        transaksi.*,
        detail_sewa.*,
        penyewa.*,
        kamera.*,
        denda.denda_id, denda.jenis_denda, denda.jumlah_denda, denda.keterangan
    FROM transaksi
    JOIN detail_sewa ON detail_sewa.detail_sewa_id = transaksi.detail_sewa_id
    JOIN penyewa ON penyewa.penyewa_id = detail_sewa.penyewa_id
    JOIN kamera ON kamera.kamera_id = detail_sewa.kamera_id
    LEFT JOIN denda ON detail_sewa.detail_sewa_id = denda.detail_sewa_id
    WHERE detail_sewa.penyewa_id = in_penyewa_id
END$$
DELIMITER ;

-- 2
DELIMITER $$
CREATE PROCEDURE sp_search_kamera(IN in_keyword VARCHAR(255))
BEGIN
    SELECT * 
    FROM kamera 
    LEFT JOIN spesifikasi ON kamera.kamera_id = spesifikasi.kamera_id
    WHERE
        merk LIKE CONCAT('%', in_keyword, '%') OR        
        harga_sewa_perhari LIKE CONCAT('%', in_keyword, '%') OR        
        stok LIKE CONCAT('%', in_keyword, '%') OR        
        resolusi LIKE CONCAT('%', in_keyword, '%') OR        
        sensor LIKE CONCAT('%', in_keyword, '%') OR        
        iso_max LIKE CONCAT('%', in_keyword, '%');
END$$

DELIMITER ;

-- 3
DELIMITER $$
CREATE PROCEDURE sp_update_kamera(
    IN in_kamera_id VARCHAR(255),
    IN in_merk VARCHAR(255),
    IN in_harga_sewa_perhari VARCHAR(255),
    IN in_stok VARCHAR(255),
    IN in_gambar VARCHAR(255),
    IN in_resolusi VARCHAR(255),
    IN in_sensor VARCHAR(255),
    IN in_iso_max VARCHAR(255),
)
BEGIN
    UPDATE kamera SET 
    merk = in_merk, 
    harga_sewa_perhari = in_harga_sewa_perhari, 
    stok = in_stok,
    gambar = in_gambar 
    WHERE kamera_id = in_kamera_id;

    UPDATE spesifikasi SET 
    resolusi = in_resolusi, 
    sensor = in_sensor, 
    iso_max = in_iso_max 
    WHERE kamera_id = in_kamera_id;
END$$

DELIMITER ;

--4
DELIMITER //

CREATE PROCEDURE sp_simpan_transaksi(
    IN p_penyewa_id INT,
    IN p_kamera_id INT,
    IN p_pinjam DATE,
    IN p_kembali DATE,
    IN p_jumlah INT,
    IN p_total INT,
    IN p_metode VARCHAR(50)
)
BEGIN
    INSERT INTO detail_sewa (penyewa_id, kamera_id, tanggal_pinjam, tanggal_kembali, jumlah, status)
    VALUES (p_penyewa_id, p_kamera_id, p_pinjam, p_kembali, p_jumlah, 'disewa');

    SET @last_id = LAST_INSERT_ID();

    INSERT INTO transaksi (detail_sewa_id, total, metode_pembayaran)
    VALUES (@last_id, p_total, p_metode);

    IF (SELECT stok FROM kamera WHERE kamera_id = p_kamera_id) <= p_jumlah THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stok tidak mencukupi';
    END IF;
END//

DELIMITER ;

--5
DELIMITER $$

CREATE PROCEDURE sp_cek_denda_keterlambatan()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_detail_sewa_id INT;
    DECLARE v_tanggal_kembali DATE;
    DECLARE v_hari_telat INT;
    DECLARE v_total_denda INT;

    -- Cursor ambil data dari view yang statusnya 'disewa'
    DECLARE cur CURSOR FOR 
        SELECT detail_sewa_id, tanggal_kembali 
        FROM view_kamera_sedang_disewa;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO v_detail_sewa_id, v_tanggal_kembali;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Hitung keterlambatan
        SET v_hari_telat = DATEDIFF(CURDATE(), v_tanggal_kembali);

        -- Jika telat (lebih dari 0 hari)
        IF v_hari_telat > 0 THEN
            SET v_total_denda = v_hari_telat * 10000;

            -- Cek apakah sudah ada denda untuk detail_sewa_id ini
            IF NOT EXISTS (
                SELECT 1 FROM denda WHERE detail_sewa_id = v_detail_sewa_id
            ) THEN
                -- Insert denda baru
                INSERT INTO denda (detail_sewa_id, jenis_denda, jumlah_denda, keterangan)
                VALUES (v_detail_sewa_id, 'terlambatan', v_total_denda, CONCAT('Terlambat ', v_hari_telat, ' hari'));
            END IF;
        END IF;

    END LOOP;

    CLOSE cur;
    SELECT * FROM view_kamera_sedang_disewa;
END$$

DELIMITER ;

sp_get_transaksi_by_user -> riwayat_sewa.php
sp_search_kamera -> index.php
sp_update_kamera -> admin/data_kamera.php
sp_simpan_transaksi -> admin/data_transaksi.php
sp_cek_denda_keterlambatan -> admin/dashboard.php