<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class sirkulasibarang extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'sirkulasibarang';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('barang','BARANG GUDANG'),
      array('type','FLOW'),
      array('transaksi','TRANSAKSI'),
      array('qty','QTY'),
      array('stock','STOK'),
    );
    $this->inputFields = null;
  }

  function find ($where = array()) {
    $this->db
      ->select('sirkulasibarang.*')
      ->select('baranggudang.nama')
      ->select("CONCAT (baranggudang.stock, ' ', baranggudang.satuan) AS stock", false)
      ->join('baranggudang', 'baranggudang.id=sirkulasibarang.barang');
    return parent::find($where);
  }
}
