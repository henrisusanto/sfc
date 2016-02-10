<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class produkoutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'produkoutlet';
    $this->thead = array(
      array('outlet','OUTLET'),
      array('produk','PRODUK'),
      array('stock','STOK'),
    );
    $this->inputFields = array(
      0 => array('outlet', 'NAMA OUTLET'),
      1 => array('produk', 'NAMA PRODUK')
    );

    $this->inputFields[0][2][0] = '';
    foreach ($this->findAnother('outlet') as $item)
      $this->inputFields[0][2][$item->id] = $item->nama;

    $this->inputFields[1][2][0] = '';
    foreach ($this->findAnother('produk') as $item)
      $this->inputFields[1][2][$item->id] = $item->nama;
  }

  function find ($where = array()) {
    $this->db
      ->select('produkoutlet.*')
      ->select("CONCAT(produkoutlet.stock, ' PCs') as stock", false)
      ->select('outlet.nama as outlet')
      ->join('outlet','produkoutlet.outlet=outlet.id')
      ->select('produk.nama as produk')
      ->join('produk','produkoutlet.produk=produk.id');
    return parent::find($where);
  }
}
