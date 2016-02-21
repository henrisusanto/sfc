<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class setoran extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'setoran';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('outlet','OUTLET'),
      array('nominal','SETORAN'),
    );
    $this->inputFields = array(
      0 => array('waktu', 'TANGGAL'),
      1 => array('outlet', 'OUTLET'),
    );
    $this->buildRelation($this->inputFields[1][2], 'outlet');

    $this->expandables = array();
    $this->expandables[0] = array(
      'label' => 'LAPORAN PENJUALAN',
      'fields' => array (
        0 => array('setoranpenjualan[produk][]', 'PRODUK'),
        1 => array('setoranpenjualan[qty][]', 'JUMLAH'),
      )
    );
    $this->buildRelation($this->expandables[0]['fields'][0][2], 'produk');

    $this->expandables[1] = array(
      'label' => 'LAPORAN PENGELUARAN',
      'fields' => array (
        0 => array('setoranpengeluaran[item][]', 'ITEM'),
        1 => array('setoranpengeluaran[nominal][]', 'NOMINAL'),
      )
    );

    $this->expandables[2] = array(
      'label' => 'LAPORAN SISA PRODUK',
      'fields' => array (
        0 => array('setoransisaproduk[produk][]', 'PRODUK'),
        1 => array('setoransisaayam[qty][]', 'QTY'),
      )
    );
    $this->buildRelation($this->expandables[2]['fields'][0][2], 'produk');

    $this->expandables[3] = array(
      'label' => 'LAPORAN SISA BAHAN',
      'fields' => array (
        0 => array('setoransisabarang[barang][]', 'BAHAN'),
        1 => array('setoransisabarang[qty][]', 'QTY'),
      )
    );
    $this->buildRelation($this->expandables[3]['fields'][0][2], 'baranggudang');

  }

  function save ($data) {
    if (isset($data['id'])) die('x');

    $waktu = $data['waktu'];
    $outlet = $data['outlet'];
    $transaksi = 'SETORAN';
    $total = 0;
    $prices = array();
    foreach ($this->db->get('produk')->result() as $product) 
      $prices[$product->id] = $product->harga;
    foreach ($data['setoranpenjualan']['produk'] as $index => $produk)
      $total += $prices[$produk] * $data['setoranpenjualan']['qty'][$index];
    foreach ($data['setoranpengeluaran']['nominal'] as $index => $nominal) 
      $total -= $nominal;

    $this->db->insert('setoran', array(
      'outlet' => $outlet,
      'waktu' => $waktu,
      'nominal' => $total
    ));
    $setoranId = $this->db->insert_id();

    // penjualan, pengeluaran, sisa : produk, bahan, ayam
  }

  function find ($where = array()) {
    $this->db
      ->select('setoran.*, outlet.nama as outlet')
      ->select("CONCAT('Rp ', FORMAT(nominal, 2)) AS nominal", false)
      ->join('outlet', 'outlet.id = setoran.outlet');
    return parent::find($where);
  }
}
