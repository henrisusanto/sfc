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
      ->select('outlet.nama as outlet')
      ->select('baranggudang.nama as barang')
      ->select("CONCAT(barangoutlet.stock, ' ', baranggudang.satuan) as stock", false)
      ->join('outlet','barangoutlet.outlet=outlet.id')
      ->join('baranggudang','barangoutlet.barang=baranggudang.id', 'LEFT');
    return parent::find($where);
  }
}
