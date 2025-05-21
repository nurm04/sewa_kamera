-- View
--1
CREATE VIEW view_total_sewa_per_kamera AS
SELECT 
    COUNT(ds.kamera_id) as jumlah_sewa,
    ds.*,
    k.merk,
    k.harga_sewa_perhari,
    k.stok,
    k.gambar
FROM detail_sewa ds
JOIN kamera k ON ds.kamera_id = k.kamera_id
GROUP BY ds.kamera_id ORDER BY jumlah_sewa DESC;

--2
CREATE VIEW view_all_data_transaksi AS
SELECT 
    t.transaksi_id, t.total, t.metode_pembayaran,
    ds.detail_sewa_id, ds.tanggal_pinjam, ds.tanggal_kembali, ds.jumlah, ds.status,
    p.*,
    kamera.*,
    denda.denda_id, denda.jenis_denda, denda.jumlah_denda, denda.keterangan
FROM transaksi t
JOIN detail_sewa ds ON ds.detail_sewa_id = t.detail_sewa_id
JOIN penyewa p ON p.penyewa_id = ds.penyewa_id
JOIN kamera ON kamera.kamera_id = ds.kamera_id
LEFT JOIN denda ON ds.detail_sewa_id = denda.detail_sewa_id;

--3
CREATE VIEW view_all_data_kamera AS
SELECT 
    k.*,
    s.resolusi,
    s.sensor,
    s.iso_max
FROM kamera k 
LEFT JOIN spesifikasi s ON k.kamera_id = s.kamera_id

--4
CREATE VIEW view_kamera_sedang_disewa AS
SELECT 
    t.transaksi_id, t.total, t.metode_pembayaran,
    ds.detail_sewa_id, ds.tanggal_pinjam, ds.tanggal_kembali, ds.jumlah, ds.status,
    p.*,
    k.*,
    d.denda_id, d.jenis_denda, d.jumlah_denda, d.keterangan
FROM detail_sewa ds
JOIN transaksi t ON ds.detail_sewa_id = t.detail_sewa_id
JOIN penyewa p ON p.penyewa_id = ds.penyewa_id
JOIN kamera k ON k.kamera_id = ds.kamera_id
LEFT JOIN denda d ON ds.detail_sewa_id = d.detail_sewa_id
WHERE ds.status = 'disewa';

--5
CREATE VIEW view_pendapatan_per_bulan AS
SELECT 
    DATE_FORMAT(tanggal_pinjam, '%Y-%m') AS bulan,
    SUM(t.total) AS total_pendapatan
FROM transaksi t
JOIN detail_sewa ds ON ds.detail_sewa_id = t.detail_sewa_id
GROUP BY bulan
ORDER BY bulan DESC;

view_total_sewa_per_kamera -> home.php
view_all_data_transaksi -> admin/data_transaksi.php
view_all_data_kamera -> admin/data_kamera.php
view_kamera_sedang_disewa -> admin/dashboard.php 
view_pendapatan_per_bulan -> admin/dashboard.php 
