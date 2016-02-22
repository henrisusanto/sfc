<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class prosesproduksi extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'produksi';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('karyawan','PENANGGUNG JAWAB'),
      array('outlet', 'OUTLET')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('karyawan', 'PENANGGUNG JAWAB'),
      2 => array('outlet', 'OUTLET / GUDANG'),
    );
    $this->buildRelation($this->inputFields[1][2], 'karyawan');
    $this->buildRelation($this->inputFields[2][2], 'outlet');
    $this->inputFields[2][2][0] = 'GUDANG';

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'PRODUK YANG DIHASILKAN',
      'fields' => array (
        0 => array('produksiproduk[produk][]', 'NAMA PRODUK'),
        1 => array('produksiproduk[qty][]', 'JUMLAH PCS'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'produk');

    $this->expandables[1] = array(
      'label' => 'BAHAN YANG DIGUNAKAN',
      'fields' => array (
        0 => array('produksibarang[barang][]', 'BAHAN'),
        1 => array('produksibarang[qty][]', 'JUMLAH SATUAN'),
      )
    );
    $this->buildRelation($this->expandables[1]['fields'][0][2], 'baranggudang');

    $this->expandables[2] = array(
      'label' => 'AYAM YANG DIGUNAKAN ( JIKA ADA )',
      'fields' => array (
        0 => array('produksiayam[ayam][]', 'AYAM'),
        1 => array('produksiayam[pcs][]', 'JUMLAH PCS'),
        2 => array('produksiayam[kg][]', 'BERAT'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'ayam');
  }

  function save ($data) {
    if (isset($data['id'])) die('durung tak pikir');
    $waktu = $data['waktu'];
    $outlet = $data['outlet'];
    $produksiId = parent::save(array(
      'waktu' => $waktu,
      'karyawan' => $data['karyawan'],
      'outlet' => $outlet
    ));
    $transaksi = 'PRODUKSI';

    foreach ($data['produksiproduk']['produk'] as $index => $produk) {
      $qty = $data['produksiproduk']['qty'][$index];
      $fkey = $this->db->insert('produksiproduk', array(
        'produksi' => $produksiId,
        'produk' => $produk,
        'qty' => $qty
      ));
      if ($outlet==0) $this->sirkulasiProduk ($waktu, $produk, 'MASUK', $transaksi, $fkey, $qty);
      else $this->sirkulasiProdukOutlet ($waktu, $produk, 'MASUK', $transaksi, $fkey, $qty, $outlet);
    }

    foreach ($data['produksibarang']['barang'] as $index => $barang) {
      $qty = $data['produksibarang']['qty'][$index];
      $fkey = $this->db->insert('produksibarang', array(
        'produksi' => $produksiId,
        'barang' => $barang,
        'qty' => $qty
      ));
      if ($outlet==0) $this->sirkulasiBarang ($waktu, $barang, 'KELUAR', $transaksi, $fkey, $qty);
      else $this->sirkulasiBarangOutlet ($waktu, $barang, 'KELUAR', $transaksi, $fkey, $qty, $outlet);
    }

    foreach ($data['produksiayam']['ayam'] as $index => $ayam) {
      $pcs = $data['produksiayam']['pcs'][$index];
      $kg = $data['produksiayam']['kg'][$index];
      $fkey = $this->db->insert('produksiayam', array(
        'produksi' => $produksiId,
        'ayam' => $ayam,
        'pcs' => $pcs,
        'kg' => $kg
      ));
      if ($outlet==0) $this->sirkulasiAyam ($waktu, $ayam, 'KELUAR', $transaksi, $fkey, $pcs, $kg);
      else $this->sirkulasiAyamOutlet ($waktu, $ayam, 'KELUAR', $transaksi, $fkey, $pcs, $kg, $outlet);
    }
  }


  function find ($where = array()) {
    $this->db
      ->select('produksi.*')
      ->select('karyawan.nama as karyawan', false)
      ->select("IFNULL(outlet.nama, 'GUDANG') as outlet", false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('outlet', 'outlet.id = produksi.outlet', 'LEFT')
      ->join('karyawan', 'karyawan.id = produksi.karyawan', 'LEFT');
    return parent::find($where);
  }
}
