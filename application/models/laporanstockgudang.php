<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class laporanstockgudang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'baranggudang';
    $this->thead = array(
      array('nama','BARANG GUDANG'),
      array('stock','STOK'),
    );
    $this->filters = array(
      0 => array('id', 'FILTER BAHAN'),
    );
    $this->buildRelation($this->filters[0][2], 'baranggudang', array(), 'TAMPILKAN SEMUA');
    $this->buildTFoot();
  }

  function find ($where = array()) {
    $this->translateDateRange($where);
    $this->db
      ->select('*')
      ->select("CONCAT (stock, ' ', satuan) AS stock", false);
    return 
    parent::find($where);
  }
}
