<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class ayam extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'ayam';
    $this->strings = array('nama');
    $this->thead = array(
      array('nama','JENIS AYAM'),
      // array('pcs','STOK PCS (EKOR)'),
      // array('kg','STOK KG')
    );
    $this->inputFields = array(
      0 => array('nama', 'JENIS AYAM'),
    );
  }

  function find ($where = array()) {
    $this->db
      ->select('ayam.*')
      ->select("CONCAT(pcs, ' PCs') AS pcs", false)
      ->select("CONCAT(kg, ' KG') AS kg", false);
    return parent::find($where);
  }
}
