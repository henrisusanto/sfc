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
      ->select('baranggudang.nama as barang')
      ->select("CONCAT (sirkulasibarang.qty, ' ', baranggudang.satuan) AS qty", false)
      ->select("CONCAT (sirkulasibarang.stock, ' ', baranggudang.satuan) AS stock", false)
      ->join('baranggudang', 'baranggudang.id=sirkulasibarang.barang')
      ->order_by('id', 'DESC');
    return parent::find($where);
  }
}
