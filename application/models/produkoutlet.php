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

  }

  function find ($where = array()) {
    $this->db
      ->select('produkoutlet.*')
      ->select("CONCAT(produkoutlet.stock, ' PCs') as stock", false)
      ->select('outlet.nama as outlet')
      ->join('outlet','produkoutlet.outlet=outlet.id')
      ->select('produk.nama as produk')
      ->join('produk','produkoutlet.produk=produk.id', 'LEFT');
    return parent::find($where);
  }
}
