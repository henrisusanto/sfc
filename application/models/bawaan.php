<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class bawaan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'bawaan';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('outlet','OUTLET')
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('outlet', 'OUTLET'),
      2 => array('modal', 'MODAL / RECEH')
    );

    $this->buildRelation($this->inputFields[1][2], 'outlet');
    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'DAFTAR BAWAAN BARANG',
      'fields' => array (
        0 => array('bawaanbarang[barang][]', 'NAMA BARANG'),
        1 => array('bawaanbarang[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'baranggudang');
    $this->expandables[1] = array(
      'label' => 'DAFTAR BAWAAN AYAM',
      'fields' => array (
        0 => array('bawaanayam[ayam][]', 'AYAM'),
        1 => array('bawaanayam[pcs][]', 'JUMLAH'),
        2 => array('bawaanayam[kg][]', 'BERAT'),
      )
    );
    $this->buildRelation($this->expandables[1]['fields'][0][2], 'ayam', array('nama <>' => 'AYAM HIDUP'));
    $this->expandables[2] = array(
      'label' => 'DAFTAR BAWAAN PRODUK',
      'fields' => array (
        0 => array('bawaanproduk[produk][]', 'PRODUK'),
        1 => array('bawaanproduk[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'produk');
  }

  function save ($data) {
    $outlet = $data['outlet'];
    $waktu = $data['waktu'];
    $modal = $data['modal'];
    $outlet = $data['outlet'];
    $waktu = $data['waktu'];
    $modal = $data['modal'];

    $record = array(
      'outlet' => $outlet,
      'waktu' => $waktu,
      'modal' => $modal,
    );

    if (isset($data['id'])) {
      $this->delete($data['id']);
      $record['id'] = $data['id'];
    }

    $this->db->insert('bawaan', $record);
    $bawaan_id = $this->db->insert_id();

    if ($modal > 0) {
      $this->sirkulasiKeuangan ('KELUAR', 'BAWAAN', $modal, $bawaan_id, $waktu);
      $this->sirkulasiKeuanganOutlet ('MASUK', 'BAWAAN', $modal, $bawaan_id, $waktu, $outlet);
    }
    foreach ($data['bawaanbarang']['barang'] as $key => $barang) {
      if ($barang == 0) continue;
      $qty = $data['bawaanbarang']['qty'][$key];
      $this->db->insert('bawaanbarang', array(
        'bawaan' => $bawaan_id,
        'barang' => $barang,
        'qty' => $qty
      ));
      $bbarang_id = $this->db->insert_id();
      $this->sirkulasiBarang ($waktu, $barang, 'KELUAR', 'BAWAAN', $bbarang_id, $qty);
      $this->sirkulasiBarangOutlet ($waktu, $barang, 'MASUK', 'BAWAAN', $bbarang_id, $qty, $outlet);
    }
    foreach ($data['bawaanayam']['ayam'] as $index => $ayam) {
      if ($ayam == 0) continue;
      $pcs = $data['bawaanayam']['pcs'][$index];
      $kg = $data['bawaanayam']['kg'][$index];
      $this->db->insert('bawaanayam', array(
        'bawaan' => $bawaan_id,
        'ayam' => $ayam,
        'pcs' => $pcs,
        'kg' => $kg
      ));
      $bayam_id = $this->db->insert_id();
      $this->sirkulasiAyam ($waktu, $ayam, 'KELUAR', 'BAWAAN', $bayam_id, $pcs, $kg);
      $this->sirkulasiAyamOutlet ($waktu, $ayam, 'MASUK', 'BAWAAN', $bayam_id, $pcs, $kg, $outlet);
    }
    foreach ($data['bawaanproduk']['produk'] as $index => $produk) {
      if ($produk == 0) continue;
      $qty = $data['bawaanproduk']['qty'][$index];
      $this->db->insert('bawaanproduk', array(
        'bawaan' => $bawaan_id,
        'produk' => $produk,
        'qty' => $qty
      ));
      $bproduk_id = $this->db->insert_id();
      $this->sirkulasiProduk ($waktu, $produk, 'KELUAR', 'BAWAAN', $bproduk_id, $qty);
      $this->sirkulasiProdukOutlet ($waktu, $produk, 'MASUK', 'BAWAAN', $bproduk_id, $qty, $outlet);
    }
  }

  function delete ($id) {
    $bawaan = parent::findOne($id);
    $outlet = $bawaan['outlet'];
    $modal = $bawaan['modal'];
    $transaksi = 'BAWAAN BATAL';
    $waktu = date('Y-m-d H:i:s',time());
    if ($modal > 0) {
      $this->sirkulasiKeuangan ('MASUK', $transaksi, $modal, $id, $waktu);
      $this->sirkulasiKeuanganOutlet ('KELUAR', $transaksi, $modal, $id, $waktu, $outlet);
    }

    foreach ($this->db->get_where('bawaanbarang', array ('bawaan' => $id))->result() as $bbarang) {
      $this->sirkulasiBarang ($waktu, $bbarang->barang, 'MASUK', $transaksi, $bbarang->id, $bbarang->qty);
      $this->sirkulasiBarangOutlet ($waktu, $bbarang->barang, 'KELUAR', $transaksi, $bbarang->id, $bbarang->qty, $outlet);
    }

    foreach ($this->db->get_where('bawaanayam', array ('bawaan' => $id))->result() as $bayam) {
      $this->sirkulasiAyam ($waktu, $bayam->ayam, 'MASUK', $transaksi, $bayam->id, $bayam->pcs, $bayam->kg);
      $this->sirkulasiAyamOutlet ($waktu, $bayam->ayam, 'KELUAR', $transaksi, $bayam->id, $bayam->pcs, $bayam->kg, $outlet);
    }

    foreach ($this->db->get_where('bawaanproduk', array ('bawaan' => $id))->result() as $bproduk) {
      $this->sirkulasiProduk ($waktu, $bproduk->produk, 'MASUK', $transaksi, $bproduk->id, $bproduk->qty);
      $this->sirkulasiProdukOutlet ($waktu, $bproduk->produk, 'KELUAR', $transaksi, $bproduk->id, $bproduk->qty, $outlet);
    }

    return parent::delete($id);
  }

  function find ($where = array()) {
    $this->db
      ->select('bawaan.*')
      ->select('outlet.nama as outlet')
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('outlet', 'bawaan.outlet = outlet.id');
    return parent::find($where);
  }

}
