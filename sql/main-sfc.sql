-- phpMyAdmin SQL Dump
-- version 4.4.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Apr 09, 2016 at 01:50 PM
-- Server version: 5.5.42
-- PHP Version: 5.6.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `sfc`
--

-- --------------------------------------------------------

--
-- Table structure for table `ayam`
--

CREATE TABLE `ayam` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `pcs` int(11) NOT NULL,
  `kg` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ayamoutlet`
--

CREATE TABLE `ayamoutlet` (
  `id` int(11) NOT NULL,
  `ayam` int(11) NOT NULL,
  `outlet` int(11) NOT NULL,
  `pcs` int(11) NOT NULL,
  `kg` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `baranggudang`
--

CREATE TABLE `baranggudang` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `satuan` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `stock` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `barangoutlet`
--

CREATE TABLE `barangoutlet` (
  `id` int(11) NOT NULL,
  `barang` int(11) NOT NULL,
  `outlet` int(11) NOT NULL,
  `stock` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bawaan`
--

CREATE TABLE `bawaan` (
  `id` int(11) NOT NULL,
  `outlet` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `modal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bawaanayam`
--

CREATE TABLE `bawaanayam` (
  `id` int(11) NOT NULL,
  `bawaan` int(11) NOT NULL,
  `ayam` int(11) NOT NULL,
  `pcs` int(11) NOT NULL,
  `kg` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bawaanbarang`
--

CREATE TABLE `bawaanbarang` (
  `id` int(11) NOT NULL,
  `bawaan` int(11) NOT NULL,
  `barang` int(11) NOT NULL,
  `qty` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bawaanproduk`
--

CREATE TABLE `bawaanproduk` (
  `id` int(11) NOT NULL,
  `bawaan` int(11) NOT NULL,
  `produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `belanja`
--

CREATE TABLE `belanja` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `karyawan` int(11) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `belanjaayam`
--

CREATE TABLE `belanjaayam` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `karyawan` int(11) NOT NULL,
  `distributor` int(11) NOT NULL,
  `ayam` int(11) NOT NULL,
  `ekor` int(11) NOT NULL,
  `kg` double NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `belanjadetail`
--

CREATE TABLE `belanjadetail` (
  `id` int(11) NOT NULL,
  `belanja` int(11) NOT NULL,
  `distributor` int(11) NOT NULL,
  `barang` int(11) NOT NULL,
  `qty` double NOT NULL,
  `hargasatuan` int(11) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cashflow`
--

CREATE TABLE `cashflow` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `type` varchar(255) NOT NULL,
  `transaksi` varchar(255) NOT NULL,
  `fkey` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `saldo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cashflowoutlet`
--

CREATE TABLE `cashflowoutlet` (
  `id` int(11) NOT NULL,
  `outlet` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `type` varchar(255) NOT NULL,
  `transaksi` varchar(255) NOT NULL,
  `fkey` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `saldo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `debitur`
--

CREATE TABLE `debitur` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `saldo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `distributor`
--

CREATE TABLE `distributor` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `internal`
--

CREATE TABLE `internal` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `source` int(11) NOT NULL,
  `destination` int(11) NOT NULL,
  `receh` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `internalayam`
--

CREATE TABLE `internalayam` (
  `id` int(11) NOT NULL,
  `internal` int(11) NOT NULL,
  `ayam` int(11) NOT NULL,
  `pcs` int(11) NOT NULL,
  `kg` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `internalbarang`
--

CREATE TABLE `internalbarang` (
  `id` int(11) NOT NULL,
  `internal` int(11) NOT NULL,
  `barang` int(11) NOT NULL,
  `qty` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `internalproduk`
--

CREATE TABLE `internalproduk` (
  `id` int(11) NOT NULL,
  `internal` int(11) NOT NULL,
  `produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `komposisi`
--

CREATE TABLE `komposisi` (
  `id` int(11) NOT NULL,
  `produk` int(11) NOT NULL,
  `ayam` int(11) NOT NULL,
  `barang` int(11) NOT NULL,
  `qty` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `outlet`
--

CREATE TABLE `outlet` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `saldo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pemasukan`
--

CREATE TABLE `pemasukan` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `karyawan` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `nominal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pemotongan`
--

CREATE TABLE `pemotongan` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `karyawan` int(11) NOT NULL,
  `bahanpcs` int(11) NOT NULL,
  `bahankg` double NOT NULL,
  `hasilpcs` int(11) NOT NULL,
  `hasilkg` double NOT NULL,
  `avg` double NOT NULL,
  `per5kg` double NOT NULL,
  `susud` double NOT NULL,
  `kepasar` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pemotongandetail`
--

CREATE TABLE `pemotongandetail` (
  `id` int(11) NOT NULL,
  `pemotongan` int(11) NOT NULL,
  `ayam` int(11) NOT NULL,
  `pcs` int(11) NOT NULL,
  `kg` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `karyawan` int(11) NOT NULL,
  `total` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pengeluarandetail`
--

CREATE TABLE `pengeluarandetail` (
  `id` int(11) NOT NULL,
  `pengeluaran` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `nominal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `outlet` int(11) NOT NULL,
  `customer` varchar(255) NOT NULL,
  `total` int(11) NOT NULL,
  `lunas` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pesananayam`
--

CREATE TABLE `pesananayam` (
  `id` int(11) NOT NULL,
  `pesanan` int(11) NOT NULL,
  `ayam` int(11) NOT NULL,
  `pcs` int(11) NOT NULL,
  `kg` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pesananbarang`
--

CREATE TABLE `pesananbarang` (
  `id` int(11) NOT NULL,
  `pesanan` int(11) NOT NULL,
  `barang` int(11) NOT NULL,
  `qty` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pesananbayar`
--

CREATE TABLE `pesananbayar` (
  `id` int(11) NOT NULL,
  `setoran` int(11) NOT NULL,
  `pesanan` int(11) NOT NULL,
  `nominal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pesananproduk`
--

CREATE TABLE `pesananproduk` (
  `id` int(11) NOT NULL,
  `pesanan` int(11) NOT NULL,
  `produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pinjaman`
--

CREATE TABLE `pinjaman` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `type` varchar(255) NOT NULL,
  `debitur` int(11) NOT NULL,
  `nominal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `produkoutlet`
--

CREATE TABLE `produkoutlet` (
  `id` int(11) NOT NULL,
  `produk` int(11) NOT NULL,
  `outlet` int(11) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `produksi`
--

CREATE TABLE `produksi` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `karyawan` int(11) NOT NULL,
  `outlet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `produksiayam`
--

CREATE TABLE `produksiayam` (
  `id` int(11) NOT NULL,
  `produksi` int(11) NOT NULL,
  `ayam` int(11) NOT NULL,
  `pcs` int(11) NOT NULL,
  `kg` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `produksibarang`
--

CREATE TABLE `produksibarang` (
  `id` int(11) NOT NULL,
  `produksi` int(11) NOT NULL,
  `barang` int(11) NOT NULL,
  `qty` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `produksiproduk`
--

CREATE TABLE `produksiproduk` (
  `id` int(11) NOT NULL,
  `produksi` int(11) NOT NULL,
  `produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `setoran`
--

CREATE TABLE `setoran` (
  `id` int(11) NOT NULL,
  `outlet` int(11) NOT NULL,
  `karyawan` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `nominal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `setoranpengeluaran`
--

CREATE TABLE `setoranpengeluaran` (
  `id` int(11) NOT NULL,
  `setoran` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `nominal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `setoranpenjualan`
--

CREATE TABLE `setoranpenjualan` (
  `id` int(11) NOT NULL,
  `setoran` int(11) NOT NULL,
  `produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `setoransisabarang`
--

CREATE TABLE `setoransisabarang` (
  `id` int(11) NOT NULL,
  `setoran` int(11) NOT NULL,
  `barang` int(11) NOT NULL,
  `qty` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `setoransisaproduk`
--

CREATE TABLE `setoransisaproduk` (
  `id` int(11) NOT NULL,
  `setoran` int(11) NOT NULL,
  `produk` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sirkulasiayam`
--

CREATE TABLE `sirkulasiayam` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `ayam` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `transaksi` varchar(255) NOT NULL,
  `fkey` int(11) NOT NULL,
  `pcs` int(11) NOT NULL,
  `kg` double NOT NULL,
  `stockpcs` int(11) NOT NULL,
  `stockkg` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sirkulasiayamoutlet`
--

CREATE TABLE `sirkulasiayamoutlet` (
  `id` int(11) NOT NULL,
  `outlet` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `ayam` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `transaksi` varchar(255) NOT NULL,
  `fkey` int(11) NOT NULL,
  `pcs` int(11) NOT NULL,
  `kg` double NOT NULL,
  `stockpcs` int(11) NOT NULL,
  `stockkg` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sirkulasibarang`
--

CREATE TABLE `sirkulasibarang` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `barang` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `transaksi` varchar(255) NOT NULL,
  `fkey` int(11) NOT NULL,
  `qty` double NOT NULL,
  `stock` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sirkulasibarangoutlet`
--

CREATE TABLE `sirkulasibarangoutlet` (
  `id` int(11) NOT NULL,
  `outlet` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `barang` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `transaksi` varchar(255) NOT NULL,
  `fkey` int(11) NOT NULL,
  `qty` double NOT NULL,
  `stock` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sirkulasiproduk`
--

CREATE TABLE `sirkulasiproduk` (
  `id` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `produk` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `transaksi` varchar(255) NOT NULL,
  `fkey` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sirkulasiprodukoutlet`
--

CREATE TABLE `sirkulasiprodukoutlet` (
  `id` int(11) NOT NULL,
  `outlet` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `produk` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `transaksi` varchar(255) NOT NULL,
  `fkey` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `stock` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ayam`
--
ALTER TABLE `ayam`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ayamoutlet`
--
ALTER TABLE `ayamoutlet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `baranggudang`
--
ALTER TABLE `baranggudang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barangoutlet`
--
ALTER TABLE `barangoutlet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bawaan`
--
ALTER TABLE `bawaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bawaanayam`
--
ALTER TABLE `bawaanayam`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bawaanbarang`
--
ALTER TABLE `bawaanbarang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bawaanproduk`
--
ALTER TABLE `bawaanproduk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `belanja`
--
ALTER TABLE `belanja`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `belanjaayam`
--
ALTER TABLE `belanjaayam`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `belanjadetail`
--
ALTER TABLE `belanjadetail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cashflow`
--
ALTER TABLE `cashflow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cashflowoutlet`
--
ALTER TABLE `cashflowoutlet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `debitur`
--
ALTER TABLE `debitur`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `distributor`
--
ALTER TABLE `distributor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `internal`
--
ALTER TABLE `internal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `internalayam`
--
ALTER TABLE `internalayam`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `internalbarang`
--
ALTER TABLE `internalbarang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `internalproduk`
--
ALTER TABLE `internalproduk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `komposisi`
--
ALTER TABLE `komposisi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `outlet`
--
ALTER TABLE `outlet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pemasukan`
--
ALTER TABLE `pemasukan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pemotongan`
--
ALTER TABLE `pemotongan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pemotongandetail`
--
ALTER TABLE `pemotongandetail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengeluarandetail`
--
ALTER TABLE `pengeluarandetail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesananayam`
--
ALTER TABLE `pesananayam`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesananbarang`
--
ALTER TABLE `pesananbarang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesananbayar`
--
ALTER TABLE `pesananbayar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesananproduk`
--
ALTER TABLE `pesananproduk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produkoutlet`
--
ALTER TABLE `produkoutlet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produksi`
--
ALTER TABLE `produksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produksiayam`
--
ALTER TABLE `produksiayam`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produksibarang`
--
ALTER TABLE `produksibarang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produksiproduk`
--
ALTER TABLE `produksiproduk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setoran`
--
ALTER TABLE `setoran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setoranpengeluaran`
--
ALTER TABLE `setoranpengeluaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setoranpenjualan`
--
ALTER TABLE `setoranpenjualan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setoransisabarang`
--
ALTER TABLE `setoransisabarang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setoransisaproduk`
--
ALTER TABLE `setoransisaproduk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sirkulasiayam`
--
ALTER TABLE `sirkulasiayam`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sirkulasiayamoutlet`
--
ALTER TABLE `sirkulasiayamoutlet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sirkulasibarang`
--
ALTER TABLE `sirkulasibarang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sirkulasibarangoutlet`
--
ALTER TABLE `sirkulasibarangoutlet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sirkulasiproduk`
--
ALTER TABLE `sirkulasiproduk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sirkulasiprodukoutlet`
--
ALTER TABLE `sirkulasiprodukoutlet`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ayam`
--
ALTER TABLE `ayam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ayamoutlet`
--
ALTER TABLE `ayamoutlet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `baranggudang`
--
ALTER TABLE `baranggudang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `barangoutlet`
--
ALTER TABLE `barangoutlet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bawaan`
--
ALTER TABLE `bawaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bawaanayam`
--
ALTER TABLE `bawaanayam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bawaanbarang`
--
ALTER TABLE `bawaanbarang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bawaanproduk`
--
ALTER TABLE `bawaanproduk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `belanja`
--
ALTER TABLE `belanja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `belanjaayam`
--
ALTER TABLE `belanjaayam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `belanjadetail`
--
ALTER TABLE `belanjadetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cashflow`
--
ALTER TABLE `cashflow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cashflowoutlet`
--
ALTER TABLE `cashflowoutlet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `debitur`
--
ALTER TABLE `debitur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `distributor`
--
ALTER TABLE `distributor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `internal`
--
ALTER TABLE `internal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `internalayam`
--
ALTER TABLE `internalayam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `internalbarang`
--
ALTER TABLE `internalbarang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `internalproduk`
--
ALTER TABLE `internalproduk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `komposisi`
--
ALTER TABLE `komposisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `outlet`
--
ALTER TABLE `outlet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pemasukan`
--
ALTER TABLE `pemasukan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pemotongan`
--
ALTER TABLE `pemotongan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pemotongandetail`
--
ALTER TABLE `pemotongandetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pengeluarandetail`
--
ALTER TABLE `pengeluarandetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pesananayam`
--
ALTER TABLE `pesananayam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pesananbarang`
--
ALTER TABLE `pesananbarang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pesananbayar`
--
ALTER TABLE `pesananbayar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pesananproduk`
--
ALTER TABLE `pesananproduk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pinjaman`
--
ALTER TABLE `pinjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `produkoutlet`
--
ALTER TABLE `produkoutlet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `produksi`
--
ALTER TABLE `produksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `produksiayam`
--
ALTER TABLE `produksiayam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `produksibarang`
--
ALTER TABLE `produksibarang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `produksiproduk`
--
ALTER TABLE `produksiproduk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `setoran`
--
ALTER TABLE `setoran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `setoranpengeluaran`
--
ALTER TABLE `setoranpengeluaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `setoranpenjualan`
--
ALTER TABLE `setoranpenjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `setoransisabarang`
--
ALTER TABLE `setoransisabarang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `setoransisaproduk`
--
ALTER TABLE `setoransisaproduk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sirkulasiayam`
--
ALTER TABLE `sirkulasiayam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sirkulasiayamoutlet`
--
ALTER TABLE `sirkulasiayamoutlet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sirkulasibarang`
--
ALTER TABLE `sirkulasibarang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sirkulasibarangoutlet`
--
ALTER TABLE `sirkulasibarangoutlet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sirkulasiproduk`
--
ALTER TABLE `sirkulasiproduk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sirkulasiprodukoutlet`
--
ALTER TABLE `sirkulasiprodukoutlet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;