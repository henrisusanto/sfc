<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class komposisi extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'komposisi';
    $this->thead = array(
      array('baranggudang','BAHAN'),
      array('produk','NAMA PRODUK /  PAKET'),
      array('qty','HASIL PRODUKSI'),
    );
    $this->inputFields = array(
      0 => array('baranggudang', 'BAHAN PER SATUAN'),
      1 => array('produk', 'NAMA PRODUK'),
      2 => array('qty', 'HASIL PRODUKSI'),
    );

    foreach ($this->findAnother('produk') as $item)
      $this->inputFields[1][2][$item->id] = $item->nama;

    foreach ($this->findAnother('baranggudang') as $item)
      $this->inputFields[0][2][$item->id] = $item->nama;
  }

  function find ($where = array()) {
    $this->db
      ->select('komposisi.*, produk.nama as produk')
      ->select("CONCAT(1, baranggudang.satuan, ' ', baranggudang.nama) AS baranggudang", false)
      ->select("CONCAT(qty, ' PCs') AS qty", false)
      ->join('baranggudang', 'baranggudang.id = komposisi.baranggudang')
      ->join('produk', 'produk.id = komposisi.produk');
    return parent::find($where);
  }
}
