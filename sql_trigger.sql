
-- Trigger --
--1
DELIMITER //

CREATE TRIGGER trg_insert_transaksi
AFTER INSERT ON detail_sewa
FOR EACH ROW
BEGIN
    DECLARE v_kamera_id INT;
    DECLARE v_jumlah INT;

    SELECT kamera_id, jumlah 
    INTO v_kamera_id, v_jumlah
    FROM detail_sewa 
    WHERE detail_sewa_id = NEW.detail_sewa_id;

    UPDATE kamera 
    SET stok = stok - v_jumlah
    WHERE kamera_id = v_kamera_id;
END//

DELIMITER ;

--2
DELIMITER //
CREATE TRIGGER trg_log_denda_update
AFTER DELETE ON denda
FOR EACH ROW
BEGIN
    INSERT INTO log_denda_update VALUES 
    ('', OLD.denda_id, OLD.detail_sewa_id, OLD.jenis_denda, OLD.jumlah_denda, OLD.keterangan, NEW.jenis_denda, NEW.jumlah_denda, NEW.keterangan);
END//
DELIMITER ;

--3
DELIMITER //
CREATE TRIGGER trg_log_kamera_delete
AFTER DELETE ON spesifikasi
FOR EACH ROW
BEGIN
    DECLARE v_merk VARCHAR(100);
    DECLARE v_harga_sewa_perhari VARCHAR(100);
    DECLARE v_stok VARCHAR(100);
    DECLARE v_gambar VARCHAR(100);

    SELECT merk, harga_sewa_perhari, stok, gambar INTO v_merk, v_harga_sewa_perhari, v_stok, v_gambar FROM kamera WHERE kamera_id = OLD.kamera_id;
    DELETE FROM kamera WHERE kamera_id = OLD.kamera_id;

    INSERT INTO log_kamera_delete (merk, harga, stok, gambar, resolusi, sensor, iso_max)
    VALUES (v_merk, v_harga_sewa_perhari, v_stok, v_gambar, OLD.resolusi, OLD.sensor, OLD.iso_max);
END//
DELIMITER ;


--4
DELIMITER //

CREATE TRIGGER trg_status_selesai
AFTER UPDATE ON detail_sewa
FOR EACH ROW
BEGIN
    DECLARE v_kamera_id INT;
    DECLARE v_jumlah INT;

    SELECT kamera_id, jumlah 
    INTO v_kamera_id, v_jumlah
    FROM detail_sewa 
    WHERE detail_sewa_id = NEW.detail_sewa_id;

    IF NEW.status = 'selesai' THEN
        UPDATE kamera 
        SET stok = stok + v_jumlah
        WHERE kamera_id = v_kamera_id;
    END IF;
END//

DELIMITER ;