TRUNCATE TABLE `bawaan`;
TRUNCATE TABLE `bawaandetail`;
TRUNCATE TABLE `belanja`;
TRUNCATE TABLE `belanjaayam`;
TRUNCATE TABLE `belanjadetail`;
TRUNCATE TABLE `cashflow`;
TRUNCATE TABLE `pemasukan`;
TRUNCATE TABLE `pengeluaran`;
TRUNCATE TABLE `pengeluarandetail`;
TRUNCATE TABLE `pinjaman`;
TRUNCATE TABLE `produkoutlet`;
TRUNCATE TABLE `setoran`;
TRUNCATE TABLE `sirkulasibarang`;
TRUNCATE TABLE `sirkulasiayam`;
TRUNCATE TABLE `sirkulasiproduk`;
UPDATE `produk` SET stock = 0;
UPDATE `ayam` SET pcs = 0, kg = 0;
UPDATE `baranggudang` SET stock = 0;
UPDATE `debitur` SET saldo = 0;