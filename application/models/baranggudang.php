<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class baranggudang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'baranggudang';
    $this->thead = array(
      array('nama','NAMA BARANG'),
      array('type','GUDANG'),
      array('stock','STOK')
    );
    $this->inputFields = array(
      0 => array('nama', 'NAMA BARANG'),
      1 => array('satuan', 'SATUAN'),
      2 => array('type', 'GUDANG', 
        array('DEPAN' => 'DEPAN', 'BELAKANG' => 'BELAKANG')),
    );
  }

  function find ($where = array()) {
    $this->db
      ->select('*')
      ->select("CONCAT (stock, ' ', satuan) AS stock", false);
    return parent::find($where);
  }
}
