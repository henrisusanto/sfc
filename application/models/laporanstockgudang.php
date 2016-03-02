<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporanstockgudang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'baranggudang';
    $this->thead = array();
    $this->thead[0] = array(
      array('nama','BAHAN'),
      array('type','GUDANG'),
      array('stock','STOK GUDANG'),
    );
    $this->thead[1] = array(
      array('nama','AYAM MENTAH'),
      array('pcs','STOK ( PCs )'),
      array('kg','STOK ( Kg )'),
    );
    $this->thead[2] = array(
      array('nama','PRODUK'),
      array('type','JENIS'),
      array('stock','STOK GUDANG'),
    );
    $this->filters = array();
    $this->tfoot = array();
    foreach ($this->thead as $index => $th) {
      foreach ($th as $t => $h) {
        $this->tfoot[$index][$t] = '';
      }
      $this->tfoot[$index][] = '';
    }
  }

  function find ($where = array()) {
    $tbody = array ();
    $tbody[0] = $this->db->select("*, CONCAT(stock, ' ', satuan) as stock", false)->get('baranggudang')->result();
    $tbody[1] = $this->db->select("*, CONCAT(pcs, ' PCs') as pcs, CONCAT(kg, ' Kg') as kg", false)->get('ayam')->result();
    $tbody[2] = $this->db->select("*, CONCAT(stock, ' PCs') as stock", false)->get('produk')->result();
    return $tbody;
  }
}
