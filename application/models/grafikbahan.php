<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class grafikbahan extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'belanja';
    $this->filters = array(
      0 => array('barang', 'FILTER BAHAN'),
      1 => array('since', 'SEJAK TANGGAL'),
      2 => array('until', 'HINGGA TANGGAL'),
    );
    $this->buildRelation($this->filters[0][2], 'baranggudang', array(), 'TAMPILKAN SEMUA');
  }

  function find ($where = array()) {
    $this->translateDateRange($where);
    $this->db->join('belanjadetail', 'belanjadetail.belanja=belanja.id', 'LEFT');
    $this->db->join('baranggudang', 'belanjadetail.barang=baranggudang.id', 'LEFT');
    return parent::find($where);
  }
}
