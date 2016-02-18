<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class barangoutlet extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'barangoutlet';
    $this->thead = array(
      array('outlet','OUTLET'),
      array('barang','BARANG'),
      array('stock','STOK'),
    );
    $this->inputFields = array(
      0 => array('outlet', 'NAMA OUTLET'),
      1 => array('barang', 'NAMA barang')
    );

  }

  function find ($where = array()) {
    $this->db
      ->select('barangoutlet.*')
      ->select("CONCAT(barangoutlet.stock, ' PCs') as stock", false)
      ->select('outlet.nama as outlet')
      ->join('outlet','barangoutlet.outlet=outlet.id')
      ->select('baranggudang.nama as barang')
      ->join('baranggudang','barangoutlet.barang=baranggudang.id', 'LEFT');
    return parent::find($where);
  }
}
