<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class transaksiinternal extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'internal';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('source','OUTLET ASAL'),
      array('destination','OUTLET TUJUAN')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('source', 'OUTLET ASAL'),
      2 => array('destination', 'OUTLET TUJUAN'),
      3 => array('receh', 'RECEH ( JIKA ADA )'),
    );
    $this->buildRelation($this->inputFields[1][2], 'outlet');
    $this->buildRelation($this->inputFields[2][2], 'outlet');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'BAHAN YANG DIKIRIM',
      'fields' => array (
        0 => array('internalbarang[barang][]', 'NAMA BARANG'),
        1 => array('internalbarang[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'baranggudang');

    $this->expandables[1] = array(
      'label' => 'AYAM YANG DIKIRIM',
      'fields' => array (
        0 => array('internalayam[ayam][]', 'AYAM'),
        1 => array('internalayam[pcs][]', 'JUMLAH'),
        2 => array('internalayam[kg][]', 'BERAT'),
      )
    );
    $this->buildRelation($this->expandables[1]['fields'][0][2], 'ayam', array('nama <>' => 'AYAM HIDUP'));

    $this->expandables[2] = array(
      'label' => 'PRODUK YANG DIKIRIM',
      'fields' => array (
        0 => array('internalproduk[produk][]', 'NAMA PRODUK'),
        1 => array('internalproduk[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'produk');
  }

  function save ($data) {
    if (isset($data['id'])) die('x');
    $waktu = $data['waktu'];
    $source = $data['source'];
    $destination = $data['destination'];
    $receh = $data['receh'];
    $internalId = parent::save(array( 
      'waktu' => $waktu,
      'source'=> $source,
      'destination' => $destination,
      'receh' => $receh
    ));
    $transaksi = 'ANTAR OUTLET';
    if ($receh > 0) {
      $this->sirkulasiKeuanganOutlet ('KELUAR', $transaksi, $receh, $internalId, $waktu, $source);
      $this->sirkulasiKeuanganOutlet ('MASUK', $transaksi, $receh, $internalId, $waktu, $destination);
    }

    foreach ($data['internalbarang']['barang'] as $index => $barang) {
      if ($barang == 0) continue;
      $qty = $data['internalbarang']['qty'][$index];
      $fkey = $this->db->insert('internalbarang', array(
        'internal' => $internalId,
        'barang' => $barang,
        'qty' => $qty
      ));
      $this->sirkulasiBarangOutlet ($waktu, $barang, 'KELUAR', $transaksi, $fkey, $qty, $source);
      $this->sirkulasiBarangOutlet ($waktu, $barang, 'MASUK', $transaksi, $fkey, $qty, $destination);
    }

    foreach ($data['internalayam']['ayam'] as $index => $ayam) {
      if ($ayam == 0) continue;
      $pcs = $data['internalayam']['pcs'][$index];
      $kg = $data['internalayam']['kg'][$index];
      $fkey = $this->db->insert('internalayam', array(
        'internal' => $internalId,
        'ayam' => $ayam,
        'pcs' => $pcs,
        'kg' => $kg
      ));
      $this->sirkulasiAyamOutlet ($waktu, $ayam, 'KELUAR', $transaksi, $fkey, $pcs, $kg, $source);
      $this->sirkulasiAyamOutlet ($waktu, $ayam, 'MASUK', $transaksi, $fkey, $pcs, $kg, $destination);
    }

    foreach ($data['internalproduk']['produk'] as $index => $produk) {
      if ($produk == 0) continue;
      $qty = $data['internalproduk']['qty'][$index];
      $fkey = $this->db->insert('internalproduk', array(
        'internal' => $internalId,
        'produk' => $produk,
        'qty' => $qty
      ));
      $this->sirkulasiProdukOutlet ($waktu, $produk, 'KELUAR', $transaksi, $fkey, $qty, $source);
      $this->sirkulasiprodukOutlet ($waktu, $produk, 'MASUK', $transaksi, $fkey, $qty, $destination);
    }
  }

  function find ($where = array()) {
    $this->db
      ->select('internal.*')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->select('source.nama as source', false)
      ->select('destination.nama as destination', false)
      ->join('outlet as source', 'internal.source = source.id', 'LEFT')
      ->join('outlet as destination', 'internal.destination = destination.id', 'LEFT');
    return parent::find($where);
  }
}
