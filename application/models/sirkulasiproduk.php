<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class sirkulasiproduk extends my_model {

  function __construct () {
    parent::__construct();
    $this->table = 'sirkulasiproduk';
    $this->thead = array(
      array('waktu','TANGGAL'),
      array('produk','NAMA PRODUK'),
      array('type','FLOW'),
      array('transaksi','TRANSAKSI'),
      array('qty','QTY'),
      array('stock','STOK'),
    );
    $this->inputFields = null;
  }

  function find ($where = array()) {
    $this->db
      ->select('sirkulasiproduk.*')
      ->select('produk.nama as produk')
      ->select("CONCAT (sirkulasiproduk.qty, ' PCs') AS qty", false)
      ->select("CONCAT (sirkulasiproduk.stock, ' PCs') AS stock", false)
      ->select("DATE_FORMAT(waktu,'%d %b %Y %T') AS waktu", false)
      ->join('produk', 'produk.id=sirkulasiproduk.produk')
      ->order_by('id', 'ASC');
    return parent::find($where);
  }
}
